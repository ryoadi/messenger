<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="relative flex flex-col h-full overflow-y-auto pr-2">
    <div class="sticky top-0 py-1 dark:bg-zinc-900 z-10">
        <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" class="mb-2" />
    </div>

    @for ($index = 0; $index < 10; $index++)
        <a href="/chat/1" wire:navigate>
            <flux:profile :name="'username'" :chevron="false" class="w-full" />
        </a>
    @endfor
</div>
