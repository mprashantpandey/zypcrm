<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadSecurityService
{
    /**
     * @param UploadedFile|TemporaryUploadedFile $file
     */
    public function validateImageUpload($file): array
    {
        return $this->validateByPolicy($file, 'image');
    }

    /**
     * @param UploadedFile|TemporaryUploadedFile $file
     */
    public function validateDocumentUpload($file): array
    {
        return $this->validateByPolicy($file, 'document');
    }

    /**
     * @param UploadedFile|TemporaryUploadedFile $file
     */
    private function validateByPolicy($file, string $policyKey): array
    {
        $policy = config("upload_security.{$policyKey}", []);
        $allowedExt = array_map('strtolower', (array) ($policy['extensions'] ?? []));
        $allowedMime = array_map('strtolower', (array) ($policy['mime_types'] ?? []));
        $maxKb = (int) ($policy['max_kb'] ?? 0);

        $path = $file->getRealPath();
        if (! is_string($path) || $path === '' || ! is_file($path)) {
            return [false, 'Upload file could not be inspected.'];
        }

        $sizeKb = (int) ceil((filesize($path) ?: 0) / 1024);
        if ($maxKb > 0 && $sizeKb > $maxKb) {
            return [false, "File exceeds max allowed size of {$maxKb}KB."];
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($ext, $allowedExt, true)) {
            return [false, 'File extension is not allowed by security policy.'];
        }

        $finfoMime = strtolower((string) (new \finfo(FILEINFO_MIME_TYPE))->file($path));
        if (! in_array($finfoMime, $allowedMime, true)) {
            return [false, 'File type does not match allowed MIME policy.'];
        }

        // Simple content signature check to block obvious script payloads.
        $sample = strtolower((string) file_get_contents($path, false, null, 0, 2048));
        $blockedSnippets = ['<?php', '<script', 'eval(', 'base64_decode('];
        foreach ($blockedSnippets as $needle) {
            if (Str::contains($sample, $needle)) {
                return [false, 'File content failed security scan.'];
            }
        }

        return [true, null];
    }
}
