<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex flex-col gap-2 h-full overflow-y-auto pr-2">
    <div class="py-1 dark:bg-zinc-900 z-10 space-y-2 pl-1">
        <flux:button.group>
            <flux:modal.trigger name="room-create">
                <flux:button icon="plus" size="sm" />    
            </flux:modal.trigger>
            <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" />    
        </flux:button.group>
    </div>

    <flux:modal name="room-create" variant="flyout" position="left" class="[&[data-open]]:flex flex-col gap-2 max-w-1/4">
        <flux:heading size="lg">{{ __('Create a new room') }}</flux:heading>
        <div class="space-y-2">
            <flux:button type="submit" variant="primary" size="sm" class="w-full">{{ __('Create') }}</flux:button>
            <flux:input size="sm" placeholder="{{ __('Room name') }}" />

            <div class="flex gap-2 flex-wrap">
                <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
            </div>

            <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" />
        </div>

        <div class="overflow-y-auto">
            @for ($index = 0; $index < 20; $index++)
                <flux:profile :name="'username'" :chevron="false" class="w-full" />
            @endfor

            <flux:button variant="subtle" size="sm" class="w-full mt-2">{{ __('Load more') }}</flux:button>
        </div>
    </flux:modal>

    <div class="overflow-y-auto">
        @for ($index = 0; $index < 10; $index++)
        <a href="/chat/1" wire:navigate>
            <flux:profile :name="'username'" :chevron="false" class="w-full" />
        </a>
        @endfor

        <flux:button variant="subtle" size="sm" class="w-full mt-2">{{ __('Load more') }}</flux:button>
    </div>
</div>
