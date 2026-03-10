<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use App\Services\UploadSecurityService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class BlogPosts extends Component
{
    use WithPagination;
    use WithFileUploads;

    public ?int $editingId = null;
    public string $title = '';
    public string $excerpt = '';
    public string $body = '';
    public bool $is_published = false;
    public $heroImage;
    public ?string $existingImagePath = null;

    public function create(): void
    {
        $this->resetForm();
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $post = BlogPost::findOrFail($id);
        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->excerpt = (string) $post->excerpt;
        $this->body = $post->body;
        $this->is_published = (bool) $post->is_published;
        $this->existingImagePath = $post->image_path;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string|min:50',
            'heroImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $slugBase = Str::slug($this->title);
        $slug = $slugBase !== '' ? $slugBase : Str::random(8);

        if ($this->editingId) {
            $post = BlogPost::findOrFail($this->editingId);
        } else {
            $post = new BlogPost(['author_id' => Auth::id()]);
        }

        if (! $this->editingId || $post->slug === null) {
            $post->slug = $this->uniqueSlug($slug, $post->id ?? null);
        }

        $post->title = $this->title;
        $post->excerpt = $this->excerpt !== '' ? $this->excerpt : null;
        $post->body = $this->body;

        if ($this->heroImage) {
            [$ok, $message] = app(UploadSecurityService::class)->validateImageUpload($this->heroImage);
            if (! $ok) {
                $this->addError('heroImage', $message ?: 'Image failed security checks.');
                return;
            }

            $path = $this->heroImage->store('blog-hero', 'public');
            $post->image_path = $path;
            $this->existingImagePath = $path;
        }

        if ($this->is_published && ! $post->is_published) {
            $post->is_published = true;
            $post->published_at = now();
        } elseif (! $this->is_published) {
            $post->is_published = false;
        }

        $post->save();

        $this->resetForm();
        $this->editingId = null;

        $this->dispatch('notify', type: 'success', message: 'Blog post saved.');
    }

    public function delete(int $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Blog post archived.');
    }

    private function resetForm(): void
    {
        $this->title = '';
        $this->excerpt = '';
        $this->body = '';
        $this->is_published = false;
        $this->heroImage = null;
        $this->existingImagePath = null;
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $counter = 1;

        while (
            BlogPost::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    public function render()
    {
        $posts = BlogPost::query()
            ->latest('published_at')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.blog-posts', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'header' => 'Platform Blog',
        ]);
    }
}

