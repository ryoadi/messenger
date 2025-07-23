<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <main class="flex flex-col h-full">
            <div class="pb-2 grow">
                
            </div>

            <flux:input placeholder="{{ __('Say something...') }}" />
        </main>
    @endvolt
</x-layouts.app>
