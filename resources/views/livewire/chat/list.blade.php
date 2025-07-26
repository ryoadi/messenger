<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="flex flex-col gap-2 h-full overflow-y-auto pr-2">
    <div class="py-1 dark:bg-zinc-900 z-10 space-y-2 pl-1">
        <flux:button.group>
            <flux:button icon="plus" size="sm" />    
            <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" />    
        </flux:button.group>
    </div>

    <div class="overflow-y-auto">
        @for ($index = 0; $index < 10; $index++)
        <a href="/chat/1" wire:navigate>
            <flux:profile :name="'username'" :chevron="false" class="w-full" />
        </a>
        @endfor

        <flux:button variant="subtle" size="sm" class="w-full mt-2">{{ __('Load more') }}</flux:button>
    </div>
</div>
