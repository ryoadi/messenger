<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <div class="relative flex flex-col h-full">
            <header class="flex gap-2 items-center sticky top-0 pb-3 -mt-20 pt-15 lg:pt-6 lg:-mt-8 z-10 dark:bg-zinc-800">
                <flux:avatar circle name="username" />
                <flux:heading size="xl"><flux:link href="#" variant="ghost">username</flux:link></flux:heading>
            </header>

            <main class="pb-2 grow flex flex-col-reverse gap-3 overflow-y-auto">
                <!-- own -->
                <livewire:chat.message :own="true" />

                <!-- theirs -->
                 @for ($index = 0; $index < 20; $index++)
                    <livewire:chat.message />
                 @endfor

                <flux:separator variant="subtle" text="{{ __('Start conversation') }}" />

                <flux:button variant="subtle" size="sm" class="w-full">{{ __('Load more') }}</flux:button>
            </main>

            <footer class="sticky bottom-0 pb-6 pt-2 -mb-6 lg:-mb-8 dark:bg-zinc-800">
                
                <flux:input.group>
                    <flux:button icon="paper-clip" />
                    <flux:button icon="face-smile" />
                    <flux:input placeholder="{{ __('Say something...') }}" />
                </flux:input.group>
            </footer>
        </div>
    @endvolt
</x-layouts.app>
