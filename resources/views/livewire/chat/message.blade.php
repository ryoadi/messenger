<?php

use App\Actions\Chat\DeleteMessage;
use App\Actions\Chat\UpdateMessage;
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

    public string $editedMessage = '';

    public function mount(): void
    {
        $this->isOwned = Gate::allows('manage', $this->message);
        $this->editedMessage = $this->message->content;
    }

    public function edit(UpdateMessage $update): void
    {
        $this->message = $update($this->message, $this->editedMessage);
    }

    public function delete(DeleteMessage $delete): void
    {
        $delete($this->message);

        // Notify parent components (e.g., room) to remove this message from their lists
        $this->dispatch('chat:message-deleted', id: (int) $this->message->getKey());
    }

}; ?>

<div x-data="{ editing: false, width: null }"
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
                    <form class="space-y-2 min-w-100" x-bind:style="width ? `width: ${width}` : ''"
                          wire:submit.prevent="edit"
                    >
                        <flux:textarea wire:model="editedMessage" rows="1" class="w-full"/>
                        <div class="flex gap-2 justify-end">
                            <flux:button size="sm" variant="subtle" type="button"
                                         x-on:click="editing=false;">{{ __('Cancel') }}</flux:button>
                            <flux:button size="sm" variant="primary" type="submit"
                                         x-on:click="editing=false">{{ __('Save') }}</flux:button>
                        </div>
                    </form>
                </div>
            @endif
            <p x-show="!editing" x-ref="content" class="whitespace-pre-line">{{ $message->content }}</p>
        </flux:text>
    </div>

    <flux:dropdown class="hidden">
        <flux:button size="xs" variant="ghost" icon="ellipsis-vertical"/>

        <flux:menu>
            @if ($isOwned)
                <flux:menu.item icon="pencil" x-on:click="editing=true">{{ __('Edit') }}</flux:menu.item>
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
