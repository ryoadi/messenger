<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <main class="flex flex-col h-full">
            <div class="pb-2 grow flex flex-col-reverse gap-3">
                <!-- own -->
                <livewire:chat.message :own="true" />

                <!-- theirs -->
                <livewire:chat.message />

                <flux:separator variant="subtle" text="{{ __('Start conversation') }}" />

                <flux:button variant="subtle" size="sm" class="w-full">{{ __('Load more') }}</flux:button>
            </div>

            <flux:input placeholder="{{ __('Say something...') }}" />
        </main>
    @endvolt
</x-layouts.app>
