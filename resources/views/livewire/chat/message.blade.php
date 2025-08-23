<?php

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {

    #[Locked]
    public bool $isOwned = false;

    #[Locked]
    public bool $group = false;

    #[Locked]
    public ChatMessage $message;

    public string $contentDraft = '';

    public function mount(): void
    {
        $this->isOwned = Gate::allows('manage', $this->message);
    }

    public function saveEdit(string $content): void
    {
        abort_unless($this->isOwned, 403);

        $validated = validator(
            ['content' => $content],
            ['content' => ['required', 'string']]
        )->validate();

        $newContent = trim((string) $validated['content']);

        // Only update if changed
        if ($newContent !== (string) $this->message->content) {
            $this->message->update(['content' => $newContent]);
            $this->dispatch('message-updated', id: $this->message->getKey());
        }
    }

    public function delete(): void
    {
        // Authorize using computed ownership
        abort_unless($this->isOwned, 403);

        $id = $this->message->getKey();
        $this->message->delete();

        // Let any parent/listeners know so they can update UI
        $this->dispatch('message-deleted', id: $id);
    }

}; ?>

<div x-data="{ editing: false, initial: @js($message->content), draft: @js($message->content), width: null }"
     x-init="$nextTick(() => { width = $refs.content ? ($refs.content.offsetWidth + 'px') : null })"
     class="flex gap-2 [&_[data-open]]:block hover:[&_[data-flux-dropdown]]:block {{ $isOwned ? 'flex-row-reverse' : '' }}"
>
    @if($group)
        <flux:avatar circle badge badge:circle badge:color="green" :name="$message->user?->name ?? 'User'" size="sm"/>
    @endif

    <div class="px-2 py-2 rounded-md {{ $isOwned ? 'dark:bg-zinc-600' : 'dark:bg-zinc-700'}}">
        <flux:text size="sm" variant="subtle" class="text-end">
            <time
                datetime="{{ $message->created_at->toAtomString() }}">{{ $message->created_at?->format('H:i') }}</time>
        </flux:text>
        <flux:text class="space-y-2">
            {{--            @if($own)--}}
            {{--                --}}{{-- image --}}
            {{--                <img src="https://placehold.co/600x400" alt="image" class="rounded-md">--}}

            {{--                --}}{{-- reply --}}
            {{--                <blockquote class="border-l-4 pl-2">--}}
            {{--                    <flux:link variant="subtle">@mention</flux:link>--}}
            {{--                    <p>reply message</p>--}}
            {{--                </blockquote>--}}

            {{--                --}}{{-- mention --}}
            {{--                <flux:link variant="subtle">@mention</flux:link>--}}
            {{--            @endif--}}

            @if ($isOwned)
                <div x-show="editing" x-cloak>
                    <div class="space-y-2 min-w-100" x-bind:style="width ? `width: ${width}` : ''">
                        <flux:textarea x-model="draft" rows="1" class="w-full"/>
                        <div class="flex gap-2 justify-end">
                            <flux:button size="sm" variant="subtle"
                                         x-on:click="editing=false; draft=initial">{{ __('Cancel') }}</flux:button>
                            <flux:button size="sm" variant="primary"
                                         x-on:click="$wire.saveEdit(draft).then(() => { editing=false })">{{ __('Save') }}</flux:button>
                        </div>
                    </div>
                </div>
            @endif
            <p x-show="!editing" x-ref="content" class="whitespace-pre-line">{{ $message->content }}</p>
        </flux:text>
    </div>

    <flux:dropdown class="hidden">
        <flux:button size="xs" variant="ghost" icon="ellipsis-vertical"/>

        <flux:menu>
            @if ($isOwned)
                <flux:menu.item icon="pencil" x-on:click="editing=true; draft=initial">{{ __('Edit') }}</flux:menu.item>
                <flux:modal.trigger name="delete-message">
                    <flux:menu.item variant="danger" icon="trash">{{ __('Delete') }}</flux:menu.item>
                </flux:modal.trigger>
            @else
                <flux:menu.item icon="arrow-uturn-left">{{ __('Reply') }}</flux:menu.item>
                <flux:menu.item icon="arrow-uturn-right">{{ __('Forward') }}</flux:menu.item>
                <flux:menu.item icon="face-smile">{{ __('React') }}</flux:menu.item>
            @endif
        </flux:menu>
    </flux:dropdown>

    @if ($isOwned)
        <flux:modal name="delete-message">
            <flux:heading class="mb-2">{{ __('Delete message') }}</flux:heading>
            <flux:text class="mb-4">{{ __('Are you sure you want to delete this message?') }}</flux:text>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:modal.close>
                    <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
                </flux:modal.close>
            </div>
        </flux:modal>
    @endif
</div>
