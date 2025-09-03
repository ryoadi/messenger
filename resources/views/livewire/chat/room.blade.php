<?php

use App\Actions\Chat\CreateMessage;
use App\Models\ChatRoom;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    #[Locked]
    public ChatRoom $room;

    #[Locked]
    public int $roomId;

    #[Locked]
    public Collection $messages;

    #[Locked]
    public string $title = '';

    public string $text = '';

    public function mount(): void
    {
        // Ensure related users are available for header rendering
        $this->room->loadMissing('users');
        $this->messages = $this->room->messages->keyBy('id');
        $this->title    = (string) $this->room->title; // model accessor
        $this->roomId = $this->room->id;
    }

    public function addMessage(CreateMessage $create): void
    {
        $message = $create($this->room, $this->text);

        // Keep in-memory list in sync for instant UI feedback
        $this->messages->unshift($message)->keyBy('id');

        // Clear input
        $this->text = '';
    }

    #[On('echo-private:chat.room.{roomId},MessageDeleted')]
    public function onMessageDeleted(array $payload): void
    {
        // Do nothing.
        // The message (somehow?) removed automatically
        // just by listening the event.
    }

}; ?>

<div class="flex flex-col gap-3 h-dvh -mt-20 lg:-my-8">
    <header class="flex gap-2 mt-15 lg:mt-3 items-center">
        <flux:modal.trigger name="profile-info">
            <flux:avatar circle badge badge:circle badge:color="green" :name="$title" href="#"/>

            <flux:heading size="xl">
                <flux:link variant="ghost">{{ $title }}</flux:link>
            </flux:heading>
        </flux:modal.trigger>

        <flux:modal name="profile-info" variant="flyout" position="right">
            <div class="flex flex-col gap-4 items-center">
                <flux:avatar circle :name="$title" size="xl"/>
                <flux:heading size="xl">{{ $title }}</flux:heading>
                <flux:text>Introduction text</flux:text>
                <flux:text>Last seen: 10 minutes ago</flux:text>
            </div>
        </flux:modal>
    </header>

    <main class="flex flex-col-reverse gap-3 grow overflow-y-auto -mr-8 pr-8" x-data x-ref="container">
        <!-- chatbox -->
        <form class="pb-3 pt-2 space-y-2 sticky bottom-0 bg-white dark:bg-zinc-800 z-10" x-data
              wire:submit.prevent="addMessage">
            <flux:button variant="ghost" size="xs" icon="chevron-down" class="w-full"
                         @click="$refs.container.scrollTo(0, $refs.container.scrollHeight)"/>

            <flux:avatar.group>
                <flux:avatar size="xs" circle name="username" tooltip="username"/>
                <flux:avatar size="xs" circle name="username" tooltip="username"/>
                <flux:avatar size="xs" circle name="username" tooltip="username"/>
                <flux:avatar size="xs" circle name="+3"/>
                <flux:text variant="subtle" class="pl-4">typing...</flux:avatar.group>
            </flux:avatar.group>

            <input type="file" x-ref="file" class="hidden"/>

            <flux:input.group>
                <flux:input placeholder="{{ __('Say something...') }}" wire:model="text"/>

                <flux:button icon="plus" @click="$refs.file.click()"/>
                <flux:button type="submit" icon="paper-airplane"/>
            </flux:input.group>
        </form>

        @foreach($messages as $message)
            <livewire:chat.message :message="$message" :key="$message->id"/>
        @endforeach

        <flux:separator variant="subtle" text="{{ __('Start conversation') }}"/>

        <div>
            <flux:button variant="subtle" size="xs" class="w-full">{{ __('Load more') }}</flux:button>
        </div>
    </main>
</div>
