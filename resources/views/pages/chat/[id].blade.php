<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <div class="relative flex flex-col h-full">
            <header class="flex gap-2 items-center sticky top-0 pb-3 -mt-20 pt-15 lg:pt-6 lg:-mt-8 z-10 dark:bg-zinc-800">
                <flux:modal.trigger name="profile-info">
                    <flux:avatar circle name="username" />
                    <flux:heading size="xl"><flux:link variant="ghost">username</flux:link></flux:heading>
                </flux:modal.trigger>

                <flux:modal name="profile-info" variant="flyout" position="right">
                    <div class="flex flex-col gap-4 items-center">
                        <flux:avatar circle name="username" size="xl" />
                        <flux:heading size="xl">username</flux:heading>
                        <flux:text>Introduction text</flux:text>
                        <flux:text>Last seen: 10 minutes ago</flux:text>
                    </div>
                </flux:modal>
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

            <form class="sticky bottom-0 pb-6 pt-2 -mb-6 lg:-mb-8 dark:bg-zinc-800" x-data>
                <input type="file" x-ref="file" class="hidden" />

                <flux:input.group>
                    <flux:input placeholder="{{ __('Say something...') }}" />

                    <flux:button icon="plus" @click="$refs.file.click()" />
                    <flux:button type="submit" icon="paper-airplane" />
                </flux:input.group>
            </form>
        </div>
    @endvolt
</x-layouts.app>
