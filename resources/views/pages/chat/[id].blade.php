<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <main class="flex flex-col h-full">
            <div class="pb-2 grow flex flex-col-reverse gap-2">
                <livewire:chat.message :own="true" />
                <livewire:chat.message />
            </div>

            <flux:input placeholder="{{ __('Say something...') }}" />
        </main>
    @endvolt
</x-layouts.app>
