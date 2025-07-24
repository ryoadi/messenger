<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <main class="relative flex flex-col h-full">
            <div class="pb-2 grow flex flex-col-reverse gap-3 overflow-y-auto">
                <!-- own -->
                <livewire:chat.message :own="true" />

                <!-- theirs -->
                 @for ($index = 0; $index < 20; $index++)
                    <livewire:chat.message />
                 @endfor

                <flux:separator variant="subtle" text="{{ __('Start conversation') }}" />

                <flux:button variant="subtle" size="sm" class="w-full">{{ __('Load more') }}</flux:button>
            </div>

            <div class="sticky bottom-0 pb-6 pt-2 -mb-6 lg:-mb-8 dark:bg-zinc-800">
                <flux:input placeholder="{{ __('Say something...') }}" />
            </div>
        </main>
    @endvolt
</x-layouts.app>
