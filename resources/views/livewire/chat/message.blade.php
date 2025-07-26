<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $own = false;
    //
}; ?>

<div class="flex gap-2 [&_[data-open]]:block hover:[&_[data-flux-dropdown]]:block {{ $own ? 'flex-row-reverse' : '' }}">
    <div class="px-2 py-2 rounded-md {{ $own ? 'dark:bg-zinc-600' : 'dark:bg-zinc-700'}}">
        <flux:text size="sm" variant="subtle" class="text-end"><time>10:00</time></flux:text>
        <flux:text>the messages</flux:text>
    </div>

    <flux:dropdown class="hidden">
        <flux:button size="xs" variant="ghost" icon="ellipsis-vertical" />

        <flux:menu>
            @if ($own)
                <flux:menu.item icon="pencil">{{ __('Edit') }}</flux:menu.item>
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

    @if ($own)
        <flux:modal name="delete-message">
            <flux:heading class="mb-2">{{ __('Delete message') }}</flux:heading>
            <flux:text class="mb-4">{{ __('Are you sure you want to delete this message?') }}</flux:text>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button>{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                
                <flux:button variant="danger">{{ __('Delete') }}</flux:button>
            </div>
        </flux:modal>
    @endif
</div>
