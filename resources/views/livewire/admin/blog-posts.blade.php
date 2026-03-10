<div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Posts</h2>
            <button wire:click="create"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500">
                New post
            </button>
        </div>

        <div class="mt-4 space-y-2">
            @forelse($posts as $post)
                <button type="button" wire:click="edit({{ $post->id }})"
                    class="w-full text-left rounded-lg border px-3 py-2 text-sm hover:bg-slate-50 {{ $editingId === $post->id ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-medium text-slate-900 truncate">{{ $post->title }}</p>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                            {{ $post->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-400 truncate">
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not published' }}
                    </p>
                </button>
            @empty
                <p class="mt-3 text-xs text-slate-400">No posts yet. Create your first article.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-slate-900">
            {{ $editingId ? 'Edit Post' : 'New Post' }}
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Keep titles clear and excerpts short for better SEO.
        </p>

        <form wire:submit="save" class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-900">Title</label>
                <input type="text" wire:model.defer="title"
                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900">Hero image</label>
                <p class="mt-1 text-xs text-slate-500">Used on blog listing and as the hero visual on the article
                    page.</p>
                <div class="mt-2 flex items-start gap-4">
                    <div class="w-32 h-20 rounded-lg border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden">
                        @if($heroImage)
                            <img src="{{ $heroImage->temporaryUrl() }}" alt="Preview"
                                class="h-full w-full object-cover">
                        @elseif($existingImagePath)
                            <img src="{{ Storage::url($existingImagePath) }}" alt="Existing image"
                                class="h-full w-full object-cover">
                        @else
                            <span class="text-[11px] text-slate-400 text-center px-2">No image</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" wire:model="heroImage"
                            class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-800" />
                        <x-input-error :messages="$errors->get('heroImage')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900">Excerpt (optional)</label>
                <textarea wire:model.defer="excerpt" rows="2"
                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Short summary shown on list pages"></textarea>
                <x-input-error :messages="$errors->get('excerpt')" class="mt-1" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900">Body</label>
                <textarea wire:model.defer="body" rows="10"
                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                    placeholder="Write your article content here..."></textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                    <input type="checkbox" wire:model.defer="is_published"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Published</span>
                </label>

                <div class="flex items-center gap-2">
                    @if($editingId)
                        <button type="button" wire:click="delete({{ $editingId }})"
                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                            Archive
                        </button>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Save Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

