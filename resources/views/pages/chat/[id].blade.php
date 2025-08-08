<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<x-layouts.app :title="__('Chat')">
    @volt
        <div class="flex flex-col gap-3 h-dvh -mt-20 lg:-my-8">
            <header class="flex gap-2 mt-15 lg:mt-3 items-center">
                <flux:modal.trigger name="profile-info">
                    <flux:avatar circle badge badge:circle badge:color="green" name="username" href="#" />

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

            <main class="flex flex-col-reverse gap-3 grow overflow-y-auto -mr-8 pr-8" x-data x-ref="container">
                <!-- chatbox -->
                <form class="pb-3 pt-2 space-y-2 sticky bottom-0 bg-white dark:bg-zinc-800 z-10" x-data>
                    <flux:button variant="ghost" size="xs" icon="chevron-down" class="w-full" @click="$refs.container.scrollTo(0, $refs.container.scrollHeight)" />

                    <input type="file" x-ref="file" class="hidden" />

                    <flux:input.group>
                        <flux:input placeholder="{{ __('Say something...') }}" />

                        <flux:button icon="plus" @click="$refs.file.click()" />
                        <flux:button type="submit" icon="paper-airplane" />
                    </flux:input.group>
                </form>

                <!-- own -->
                <livewire:chat.message :own="true" />

                <!-- theirs -->
                 @for ($index = 0; $index < 20; $index++)
                    <livewire:chat.message />
                 @endfor

                <flux:separator variant="subtle" text="{{ __('Start conversation') }}" />

                <div>
                    <flux:button variant="subtle" size="xs" class="w-full">{{ __('Load more') }}</flux:button>
                </div>
            </main>
        </div>
    @endvolt
</x-layouts.app>
