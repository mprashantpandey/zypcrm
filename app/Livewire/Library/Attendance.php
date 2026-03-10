<?php

namespace App\Livewire\Library;

use App\Models\AttendanceActionLog;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Attendance extends Component
{
    public $attendanceDate;
    public $search = '';
    public array $selectedStudentIds = [];
    public ?string $inlineMessage = null;
    public string $inlineType = 'success';

    public ?string $deviceFingerprint = null;
    public ?float $operatorLatitude = null;
    public ?float $operatorLongitude = null;

    public array $bulkConflicts = [];

    public function mount()
    {
        $this->attendanceDate = date('Y-m-d');
    }

    public function updatedAttendanceDate(): void
    {
        $this->selectedStudentIds = [];
        $this->bulkConflicts = [];
    }

    public function updatedSearch(): void
    {
        $this->selectedStudentIds = [];
    }

    public function selectAllVisible(): void
    {
        $tenantId = Auth::user()->tenant_id;

        $studentsQuery = User::where('role', 'student')
            ->whereHas('memberships', fn (Builder $q) => $q->where('tenant_id', $tenantId)->where('status', 'active'));

        if (! empty($this->search)) {
            $studentsQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        $this->selectedStudentIds = $studentsQuery->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function clearSelection(): void
    {
        $this->selectedStudentIds = [];
        $this->bulkConflicts = [];
    }

    public function markSelected(string $status): void
    {
        if (empty($this->selectedStudentIds)) {
            $this->notifyUser('error', 'Select at least one student first.');

            return;
        }

        $success = 0;
        $failed = 0;
        $this->bulkConflicts = [];

        foreach ($this->selectedStudentIds as $studentId) {
            $result = $this->applyStatusToStudent((int) $studentId, $status, silent: true);
            if ($result['ok']) {
                $success++;
            } else {
                $failed++;
                $this->bulkConflicts[] = [
                    'student_id' => (int) $studentId,
                    'message' => $result['message'] ?? 'Failed to update.',
                    'status' => $status,
                ];
            }
        }

        $this->notifyUser($failed ? 'warning' : 'success', "Marked {$success} student(s) as ".ucfirst($status).($failed ? ", {$failed} failed." : '.'));
    }

    public function loginSelected(): void
    {
        if (empty($this->selectedStudentIds)) {
            $this->notifyUser('error', 'Select at least one student first.');

            return;
        }

        $success = 0;
        $failed = 0;
        $this->bulkConflicts = [];

        foreach ($this->selectedStudentIds as $studentId) {
            $result = $this->loginStudent((int) $studentId, silent: true);
            if ($result['ok']) {
                $success++;
            } else {
                $failed++;
                $this->bulkConflicts[] = [
                    'student_id' => (int) $studentId,
                    'message' => $result['message'] ?? 'Failed to login.',
                    'status' => 'present',
                ];
            }
        }

        $this->notifyUser($failed ? 'warning' : 'success', "Logged in {$success} student(s)".($failed ? ", {$failed} failed." : '.'));
    }

    public function clockOutSelected(): void
    {
        if (empty($this->selectedStudentIds)) {
            $this->notifyUser('error', 'Select at least one student first.');

            return;
        }

        $success = 0;
        $failed = 0;
        $this->bulkConflicts = [];

        foreach ($this->selectedStudentIds as $studentId) {
            $result = $this->clockOutStudent((int) $studentId, silent: true);
            if ($result['ok']) {
                $success++;
            } else {
                $failed++;
                $this->bulkConflicts[] = [
                    'student_id' => (int) $studentId,
                    'message' => $result['message'] ?? 'Failed to clock out.',
                    'status' => 'present',
                ];
            }
        }

        $this->notifyUser($failed ? 'warning' : 'success', "Clocked out {$success} student(s)".($failed ? ", {$failed} failed." : '.'));
    }

    public function markConflictRowsAbsent(): void
    {
        if (empty($this->bulkConflicts)) {
            return;
        }

        $failedIds = collect($this->bulkConflicts)->pluck('student_id')->unique()->values()->all();
        $success = 0;

        foreach ($failedIds as $studentId) {
            $result = $this->applyStatusToStudent((int) $studentId, 'absent', silent: true);
            if ($result['ok']) {
                $success++;
            }
        }

        $this->bulkConflicts = [];
        $this->notifyUser('success', "Conflict resolver applied: {$success} student(s) marked absent.");
    }

    public function removeConflictRowsFromSelection(): void
    {
        if (empty($this->bulkConflicts)) {
            return;
        }

        $failedIds = collect($this->bulkConflicts)->pluck('student_id')->map(fn ($id) => (string) $id)->all();
        $this->selectedStudentIds = array_values(array_filter(
            $this->selectedStudentIds,
            fn ($id) => ! in_array((string) $id, $failedIds, true)
        ));
        $this->bulkConflicts = [];
        $this->notifyUser('success', 'Failed rows removed from selection.');
    }

    public function clearBulkConflicts(): void
    {
        $this->bulkConflicts = [];
    }

    public function markAttendance($studentId, $status)
    {
        $result = $this->applyStatusToStudent((int) $studentId, (string) $status, silent: false);
        if (! $result['ok']) {
            $this->notifyUser('error', $result['message']);
        }
    }

    public function login($studentId): void
    {
        $result = $this->loginStudent((int) $studentId, silent: false);
        if (! $result['ok']) {
            $this->notifyUser('error', $result['message']);
        }
    }

    public function clockOut($studentId): void
    {
        $result = $this->clockOutStudent((int) $studentId, silent: false);
        if (! $result['ok']) {
            $this->notifyUser('error', $result['message']);
        }
    }

    private function applyStatusToStudent(int $studentId, string $status, bool $silent = false): array
    {
        $tenantId = Auth::user()->tenant_id;
        if (! $this->isStudentInTenant($studentId, (int) $tenantId)) {
            return ['ok' => false, 'message' => 'Selected student is not attached to your library.'];
        }

        $securityError = $this->validateAttendanceSecurity();
        if ($securityError !== null) {
            return ['ok' => false, 'message' => $securityError];
        }

        $attendance = StudentAttendance::firstOrNew([
            'tenant_id' => $tenantId,
            'user_id' => $studentId,
            'date' => $this->attendanceDate,
        ]);

        if ($status === 'present') {
            if ($attendance->exists && $attendance->status === 'present' && ! empty($attendance->check_in)) {
                $this->logAttendanceAction($studentId, 'duplicate_mark_attempt', 'present', true, 'Duplicate present mark attempt detected.', [
                    'anomaly' => 'duplicate',
                ]);

                if (! $silent) {
                    $this->notifyUser('warning', 'Student is already marked present.');
                }

                return ['ok' => true, 'message' => 'Already present.'];
            }

            $checkIn = $attendance->check_in ?: Carbon::now()->format('H:i');
            $checkOut = $attendance->check_out;

            $conflict = $this->buildPresenceConflictMessage(
                userId: $studentId,
                date: (string) $this->attendanceDate,
                tenantId: (int) $tenantId,
                checkIn: (string) $checkIn,
                checkOut: $checkOut ? (string) $checkOut : null,
                currentAttendanceId: $attendance->exists ? (int) $attendance->id : null
            );

            if ($conflict !== null) {
                $this->logAttendanceAction($studentId, 'mark_present', 'present', false, $conflict, [
                    'anomaly' => 'cross_library_overlap',
                ]);

                return ['ok' => false, 'message' => $conflict];
            }

            $attendance->status = 'present';
            $attendance->check_in = $checkIn;
            $attendance->check_out = $checkOut;
        } else {
            $attendance->status = $status;
            $attendance->check_in = null;
            $attendance->check_out = null;
        }

        $this->applyAttendanceActionMeta($attendance);
        $attendance->save();

        $this->logAttendanceAction($studentId, 'mark_status', $status, true, 'Status updated.', [
            'check_in' => $attendance->check_in,
            'check_out' => $attendance->check_out,
        ]);

        $this->tagPatternAbuseIfNeeded($attendance);

        if (! $silent) {
            $this->notifyUser('success', 'Attendance marked as '.ucfirst($status).'.');
        }

        return ['ok' => true, 'message' => 'Success'];
    }

    public function updateTime($studentId, $field, $value)
    {
        $tenantId = Auth::user()->tenant_id;
        if (! $this->isStudentInTenant((int) $studentId, (int) $tenantId)) {
            $this->notifyUser('error', 'Selected student is not attached to your library.');

            return;
        }

        $securityError = $this->validateAttendanceSecurity();
        if ($securityError !== null) {
            $this->notifyUser('error', $securityError);

            return;
        }

        $attendance = StudentAttendance::where('tenant_id', $tenantId)
            ->where('user_id', $studentId)
            ->where('date', $this->attendanceDate)
            ->first();

        if ($attendance) {
            $newCheckIn = $field === 'check_in' ? (empty($value) ? null : $value) : ($attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : null);
            $newCheckOut = $field === 'check_out' ? (empty($value) ? null : $value) : ($attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null);

            if ($attendance->status === 'present') {
                if (empty($newCheckIn)) {
                    $this->notifyUser('error', 'Check in time is required for present status.');

                    return;
                }

                if (! empty($newCheckOut) && $this->timeToMinutes($newCheckOut) <= $this->timeToMinutes($newCheckIn)) {
                    $this->notifyUser('error', 'Check out must be later than check in.');

                    return;
                }

                $conflict = $this->buildPresenceConflictMessage(
                    userId: (int) $studentId,
                    date: (string) $this->attendanceDate,
                    tenantId: (int) $tenantId,
                    checkIn: (string) $newCheckIn,
                    checkOut: $newCheckOut,
                    currentAttendanceId: (int) $attendance->id
                );

                if ($conflict !== null) {
                    $this->logAttendanceAction((int) $studentId, 'update_time', $attendance->status, false, $conflict, [
                        'anomaly' => 'cross_library_overlap',
                        'field' => $field,
                        'value' => $value,
                    ]);
                    $this->notifyUser('error', $conflict);

                    return;
                }
            }

            $attendance->fill([$field => empty($value) ? null : $value]);
            $this->applyAttendanceActionMeta($attendance);
            $attendance->save();
            $this->logAttendanceAction((int) $studentId, 'update_time', $attendance->status, true, ucfirst(str_replace('_', ' ', $field)).' updated.', [
                'field' => $field,
                'value' => $value,
            ]);
            $this->tagPatternAbuseIfNeeded($attendance);
            $this->notifyUser('success', ucfirst(str_replace('_', ' ', $field)).' updated.');
        }
    }

    private function loginStudent(int $studentId, bool $silent = false): array
    {
        $tenantId = Auth::user()->tenant_id;
        if (! $this->isStudentInTenant($studentId, (int) $tenantId)) {
            return ['ok' => false, 'message' => 'Selected student is not attached to your library.'];
        }

        $securityError = $this->validateAttendanceSecurity();
        if ($securityError !== null) {
            return ['ok' => false, 'message' => $securityError];
        }

        $attendance = StudentAttendance::firstOrNew([
            'tenant_id' => $tenantId,
            'user_id' => $studentId,
            'date' => $this->attendanceDate,
        ]);

        if ($attendance->exists && $attendance->status === 'present' && ! empty($attendance->check_in) && empty($attendance->check_out)) {
            $this->logAttendanceAction($studentId, 'duplicate_login_attempt', 'present', true, 'Duplicate login attempt detected.', [
                'anomaly' => 'duplicate',
            ]);

            if (! $silent) {
                $this->notifyUser('warning', 'Student already checked in.');
            }

            return ['ok' => true, 'message' => 'Already checked in.'];
        }

        $checkIn = Carbon::now()->format('H:i');
        $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null;
        if ($checkOut !== null && $this->timeToMinutes($checkOut) <= $this->timeToMinutes($checkIn)) {
            $checkOut = null;
        }

        $conflict = $this->buildPresenceConflictMessage(
            userId: $studentId,
            date: (string) $this->attendanceDate,
            tenantId: (int) $tenantId,
            checkIn: $checkIn,
            checkOut: $checkOut,
            currentAttendanceId: $attendance->exists ? (int) $attendance->id : null
        );

        if ($conflict !== null) {
            $this->logAttendanceAction($studentId, 'login', 'present', false, $conflict, [
                'anomaly' => 'cross_library_overlap',
            ]);

            return ['ok' => false, 'message' => $conflict];
        }

        $attendance->status = 'present';
        $attendance->check_in = $checkIn;
        $attendance->check_out = $checkOut;
        $this->applyAttendanceActionMeta($attendance);
        $attendance->save();

        $this->logAttendanceAction($studentId, 'login', 'present', true, 'Student logged in (check-in recorded).', [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);
        $this->tagPatternAbuseIfNeeded($attendance);

        if (! $silent) {
            $this->notifyUser('success', 'Student logged in (check-in recorded).');
        }

        return ['ok' => true, 'message' => 'Success'];
    }

    private function clockOutStudent(int $studentId, bool $silent = false): array
    {
        $tenantId = Auth::user()->tenant_id;
        if (! $this->isStudentInTenant($studentId, (int) $tenantId)) {
            return ['ok' => false, 'message' => 'Selected student is not attached to your library.'];
        }

        $securityError = $this->validateAttendanceSecurity();
        if ($securityError !== null) {
            return ['ok' => false, 'message' => $securityError];
        }

        $attendance = StudentAttendance::where('tenant_id', $tenantId)
            ->where('user_id', $studentId)
            ->where('date', $this->attendanceDate)
            ->first();

        if (! $attendance || $attendance->status !== 'present' || empty($attendance->check_in)) {
            return ['ok' => false, 'message' => 'Student must be present with check-in before clock out.'];
        }

        if (! empty($attendance->check_out)) {
            if (! $silent) {
                $this->notifyUser('warning', 'Student already clocked out.');
            }

            return ['ok' => true, 'message' => 'Already clocked out.'];
        }

        $checkIn = Carbon::parse($attendance->check_in)->format('H:i');
        $checkOutTime = Carbon::now()->seconds(0);
        $checkInTime = Carbon::createFromFormat('H:i', $checkIn)->seconds(0);
        if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
            $checkOutTime = $checkInTime->copy()->addMinute();
        }
        $checkOut = $checkOutTime->format('H:i');

        if ($this->timeToMinutes($checkOut) <= $this->timeToMinutes($checkIn)) {
            return ['ok' => false, 'message' => 'Clock out must be after check in.'];
        }

        $conflict = $this->buildPresenceConflictMessage(
            userId: $studentId,
            date: (string) $this->attendanceDate,
            tenantId: (int) $tenantId,
            checkIn: $checkIn,
            checkOut: $checkOut,
            currentAttendanceId: (int) $attendance->id
        );

        if ($conflict !== null) {
            $this->logAttendanceAction($studentId, 'clock_out', 'present', false, $conflict, [
                'anomaly' => 'cross_library_overlap',
            ]);

            return ['ok' => false, 'message' => $conflict];
        }

        $attendance->check_out = $checkOut;
        $this->applyAttendanceActionMeta($attendance);
        $attendance->save();

        $this->logAttendanceAction($studentId, 'clock_out', 'present', true, 'Student clocked out.', [
            'check_out' => $checkOut,
        ]);
        $this->tagPatternAbuseIfNeeded($attendance);

        if (! $silent) {
            $this->notifyUser('success', 'Student clocked out.');
        }

        return ['ok' => true, 'message' => 'Success'];
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $studentsQuery = User::where('role', 'student')
            ->whereHas('memberships', fn (Builder $q) => $q->where('tenant_id', $tenantId)->where('status', 'active'));

        if (! empty($this->search)) {
            $studentsQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        $students = $studentsQuery->orderBy('name')->get();

        $existingAttendances = StudentAttendance::where('tenant_id', $tenantId)
            ->where('date', $this->attendanceDate)
            ->get()
            ->keyBy('user_id');

        $missedClockOutCount = StudentAttendance::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'present')
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->whereDate('date', '<', Carbon::today())
            ->count();

        $security = $this->getTenantSecuritySettings();

        return view('livewire.library.attendance', [
            'students' => $students,
            'existingAttendances' => $existingAttendances,
            'missedClockOutCount' => $missedClockOutCount,
            'securityEnabled' => [
                'ip' => (bool) ($security['enforce_ip'] ?? false),
                'device' => (bool) ($security['enforce_device'] ?? false),
                'geofence' => (bool) ($security['geofence_enabled'] ?? false),
            ],
        ])->layout('layouts.app', [
            'header' => 'Attendance Management',
        ]);
    }

    private function buildPresenceConflictMessage(
        int $userId,
        string $date,
        int $tenantId,
        string $checkIn,
        ?string $checkOut,
        ?int $currentAttendanceId = null
    ): ?string {
        $candidateStart = $this->timeToMinutes($checkIn);
        $candidateEnd = $this->timeToMinutes($checkOut ?: '23:59');

        if ($candidateStart === null || $candidateEnd === null || $candidateEnd <= $candidateStart) {
            return 'Invalid presence time window.';
        }

        $query = StudentAttendance::query()
            ->with('tenant')
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('status', 'present')
            ->where('tenant_id', '!=', $tenantId);

        if ($currentAttendanceId) {
            $query->where('id', '!=', $currentAttendanceId);
        }

        $conflicts = [];
        foreach ($query->get() as $other) {
            $otherStart = $this->timeToMinutes($other->check_in ? Carbon::parse($other->check_in)->format('H:i') : '00:00');
            $otherEnd = $this->timeToMinutes($other->check_out ? Carbon::parse($other->check_out)->format('H:i') : '23:59');

            if ($otherStart === null || $otherEnd === null || $otherEnd <= $otherStart) {
                continue;
            }

            $overlaps = $candidateStart < $otherEnd && $candidateEnd > $otherStart;
            if ($overlaps) {
                $libraryName = $other->tenant?->name ?? 'another library';
                $conflicts[] = "{$libraryName} (".$this->formatMinutes($otherStart).'-'.$this->formatMinutes($otherEnd).')';
            }
        }

        if (! empty($conflicts)) {
            return 'Student cannot be marked present in overlapping time at multiple libraries. Conflicts: '.implode(', ', $conflicts).'.';
        }

        return null;
    }

    private function timeToMinutes(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (! preg_match('/^(?<h>\d{2}):(?<m>\d{2})/', trim($value), $parts)) {
            return null;
        }

        $h = (int) $parts['h'];
        $m = (int) $parts['m'];
        if ($h < 0 || $h > 23 || $m < 0 || $m > 59) {
            return null;
        }

        return ($h * 60) + $m;
    }

    private function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function isStudentInTenant(int $studentId, int $tenantId): bool
    {
        return User::query()
            ->where('id', $studentId)
            ->where('role', 'student')
            ->whereHas('memberships', fn (Builder $q) => $q->where('tenant_id', $tenantId)->where('status', 'active'))
            ->exists();
    }

    private function getTenantSecuritySettings(): array
    {
        /** @var Tenant|null $tenant */
        $tenant = Auth::user()->tenant;
        if (! Schema::hasColumn('tenants', 'attendance_security_settings')) {
            return [];
        }

        $security = $tenant?->attendance_security_settings;

        return is_array($security) ? $security : [];
    }

    private function validateAttendanceSecurity(): ?string
    {
        /** @var Tenant|null $tenant */
        $tenant = Auth::user()->tenant;
        if (! $tenant) {
            return 'Library tenant not found.';
        }

        $security = $this->getTenantSecuritySettings();
        $ipAddress = request()->ip();

        if (($security['enforce_ip'] ?? false) === true) {
            $allowedIpText = trim((string) ($security['allowed_ips'] ?? ''));
            $allowed = collect(preg_split('/\r\n|\r|\n/', $allowedIpText ?: '') ?: [])
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values();

            if ($allowed->isEmpty()) {
                return 'Attendance security blocked: allowed IP list is empty.';
            }

            $isAllowed = $allowed->contains(function (string $rule) use ($ipAddress) {
                if (str_contains($rule, '/')) {
                    return $this->ipInCidr($ipAddress, $rule);
                }

                return $ipAddress === $rule;
            });

            if (! $isAllowed) {
                return 'Attendance blocked: your network IP is not on allowed list.';
            }
        }

        if (($security['enforce_device'] ?? false) === true) {
            if (empty($this->deviceFingerprint)) {
                return 'Attendance blocked: device fingerprint unavailable. Refresh and try again.';
            }

            if (! Schema::hasColumn('tenants', 'attendance_registered_device_hash')) {
                return 'Attendance blocked: device lock requires latest migrations.';
            }

            if (empty($tenant->attendance_registered_device_hash)) {
                $tenant->update(['attendance_registered_device_hash' => $this->deviceFingerprint]);
            } elseif (! hash_equals((string) $tenant->attendance_registered_device_hash, (string) $this->deviceFingerprint)) {
                return 'Attendance blocked: this device is not registered for your library.';
            }
        }

        if (($security['geofence_enabled'] ?? false) === true) {
            $lat = isset($security['geofence_latitude']) ? (float) $security['geofence_latitude'] : null;
            $lng = isset($security['geofence_longitude']) ? (float) $security['geofence_longitude'] : null;
            $radius = (int) ($security['geofence_radius_meters'] ?? 150);

            if ($lat === null || $lng === null) {
                return 'Attendance geofence is misconfigured. Please update library settings.';
            }

            if ($this->operatorLatitude === null || $this->operatorLongitude === null) {
                return 'Attendance blocked: location permission is required for geofence check.';
            }

            $distance = $this->distanceMeters((float) $this->operatorLatitude, (float) $this->operatorLongitude, $lat, $lng);
            if ($distance > $radius) {
                return 'Attendance blocked: current location is outside configured geofence ('.round($distance).'m > '.$radius.'m).';
            }
        }

        return null;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = array_pad(explode('/', $cidr, 2), 2, null);
        $maskBits = (int) $mask;

        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || ! filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        if ($maskBits < 0 || $maskBits > 32) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - $maskBits);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    private function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function applyAttendanceActionMeta(StudentAttendance $attendance): void
    {
        if (Schema::hasColumn('student_attendances', 'action_ip')) {
            $attendance->action_ip = request()->ip();
        }
        if (Schema::hasColumn('student_attendances', 'action_device')) {
            $attendance->action_device = $this->deviceFingerprint ?: null;
        }
        if (Schema::hasColumn('student_attendances', 'action_latitude')) {
            $attendance->action_latitude = $this->operatorLatitude;
        }
        if (Schema::hasColumn('student_attendances', 'action_longitude')) {
            $attendance->action_longitude = $this->operatorLongitude;
        }
    }

    private function logAttendanceAction(int $studentId, string $action, ?string $status, bool $success, string $message, array $meta = []): void
    {
        if (! Schema::hasTable('attendance_action_logs')) {
            return;
        }

        AttendanceActionLog::create([
            'tenant_id' => (int) Auth::user()->tenant_id,
            'user_id' => $studentId,
            'operator_id' => (int) Auth::id(),
            'date' => $this->attendanceDate,
            'action' => Str::limit($action, 40, ''),
            'status' => $status,
            'success' => $success,
            'message' => $message,
            'ip_address' => request()->ip(),
            'device_hash' => $this->deviceFingerprint,
            'latitude' => $this->operatorLatitude,
            'longitude' => $this->operatorLongitude,
            'meta' => $meta,
        ]);
    }

    private function tagPatternAbuseIfNeeded(StudentAttendance $attendance): void
    {
        if (! Schema::hasTable('attendance_action_logs')) {
            return;
        }

        $actionCount = AttendanceActionLog::query()
            ->where('tenant_id', $attendance->tenant_id)
            ->where('user_id', $attendance->user_id)
            ->whereDate('date', $attendance->date)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($actionCount < 8) {
            return;
        }

        if (! Schema::hasColumn('student_attendances', 'anomaly_flags')) {
            return;
        }

        $flags = (array) $attendance->anomaly_flags;
        $flags['pattern_abuse'] = true;
        $flags['pattern_abuse_count_last_10m'] = $actionCount;
        $flags['pattern_abuse_detected_at'] = now()->toDateTimeString();
        $attendance->anomaly_flags = $flags;
        $attendance->save();

        $this->notifyUser('warning', 'Pattern abuse warning: excessive attendance updates detected for this student.');
    }

    public function clearInlineMessage(): void
    {
        $this->inlineMessage = null;
    }

    private function notifyUser(string $type, string $message): void
    {
        $this->inlineType = $type;
        $this->inlineMessage = $message;
        $this->dispatch('notify', type: $type, message: $message);
    }
}
