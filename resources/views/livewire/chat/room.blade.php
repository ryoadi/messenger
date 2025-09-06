<?php

use App\Models\ChatRoom;
use App\Rules\HtmlFilled;
use App\Models\ChatMessage;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use App\Models\Enums\ChatRoomType;
use Illuminate\Support\Collection;
use App\Actions\Chat\CreateMessage;

new class extends Component {

    #[Locked]
    public ChatRoom $room;

    #[Locked]
    public int $roomId;

    #[Locked]
    public bool $isGroupRoom = false;

    #[Locked]
    public Collection $chatMessages;

    #[Locked]
    public string $title = '';

    public string $text = '';

    public array $typing = [];

    public function mount(): void
    {
        // Ensure related users are available for header rendering
        $this->room->loadMissing('users');
        $this->chatMessages    = $this->room->messages->keyBy('id');
        $this->title       = (string) $this->room->title; // model accessor
        $this->roomId      = $this->room->id;
        $this->isGroupRoom = $this->room->type === ChatRoomType::Group;
    }

    public function addMessage(CreateMessage $create, HtmlFilled $filledRule): void
    {
        $this->validate([
            'text' => ['required', $filledRule],
        ]);

        $message = $create($this->room, $this->text);

        // Keep in-memory list in sync for instant UI feedback
        $this->chatMessages->unshift($message)->keyBy('id');

        // Clear input
        $this->text = '';

        $this->js("channel.whisper('MessageAdded', {id: $message->id})");
    }

    #[On('echo-private:chat.{roomId},.client-MessageAdded')]
    public function appendMessage(array $payload): void
    {
        $message = ChatMessage::find($payload['id']);
        $this->chatMessages->unshift($message)->keyBy('id');
    }

    #[On('echo-private:chat.{roomId},.client-MessageUpdated')]
    #[On('echo-private:chat.{roomId},.client-MessageDeleted')]
    public function refresh(): void
    {
        // The chatMessages updated automatically on refresh.
    }

    #[On('echo-private:chat.{roomId},.client-Typing')]
    public function addTyping(array $payload): void
    {
        $this->typing[$payload['id']] = $payload['name'];
    }

    #[On('echo-private:chat.{roomId},.client-Typed')]
    public function removeTyping(array $payload): void
    {
        unset($this->typing[$payload['id']]);
    }

}; ?>

<div class="flex flex-col gap-3 h-dvh -mt-20 lg:-my-8"
     x-data="{channel: null}"
     x-init="channel = Echo.private(`chat.${$wire.roomId}`)"
>
    <header class="flex gap-2 mt-15 lg:mt-3 items-center">
        <flux:modal.trigger name="profile-info">
            <flux:avatar circle badge badge:circle badge:color="green" :name="$title" href="#"/>

            <flux:heading size="xl">
                <flux:link variant="ghost">{{ $title }}</flux:link>
            </flux:heading>
        </flux:modal.trigger>

        <flux:modal name="profile-info" variant="flyout" position="right">
            @if($isGroupRoom)
                <livewire:chat.profile.group :room="$room"/>
            @else
                <livewire:chat.profile.personal :room="$room"/>
            @endif
        </flux:modal>
    </header>

    <main class="flex flex-col-reverse gap-3 grow overflow-y-auto -mr-8 pr-8" x-data x-ref="container">
        <!-- chatbox -->
        <form class="pb-3 pt-2 space-y-2 sticky bottom-0 bg-white dark:bg-zinc-800 z-10" x-data
              wire:submit.prevent="addMessage">
            <flux:button variant="ghost" size="xs" icon="chevron-down" class="w-full"
                         @click="$refs.container.scrollTo(0, $refs.container.scrollHeight)"/>

            @if(!empty($typing))
                <flux:avatar.group>
                    @foreach($typing as $name)
                        <flux:avatar size="xs" circle :name="$name" :tooltip="$name"/>
                    @endforeach

                    @if(count($typing) > 3)
                        <flux:avatar size="xs" circle :name="'+'.count($typing) -3"/>
                    @endif

                    <flux:text variant="subtle" class="pl-4">typing...</flux:avatar.group>
                </flux:avatar.group>
            @endif

            <div class="relative">
                <x-text-editor
                    class="bg-zinc-700 pl-4 pr-8 py-2 rounded-lg"
                    wire:model="text"
                    @focus="channel.whisper('Typing', {id: {{auth()->id()}}, name: '{{auth()->user()->name}}'})"
                    @blur="channel.whisper('Typed', {id: {{auth()->id()}}})"
                />

                <div class="absolute bottom-0 right-0">
                    <flux:button variant="ghost" icon="paper-airplane" size="sm" type="submit"></flux:button>
                </div>
            </div>
        </form>

        @foreach($chatMessages as $message)
            <livewire:chat.message
                :message="$message"
                :key="$message->id.':'.$message->updated_at"
                :isGroupRoom="$isGroupRoom"
                @updated="$refresh();channel.whisper('MessageUpdated')"
                @deleted="$refresh();channel.whisper('MessageDeleted')"
            />
        @endforeach

        <flux:separator variant="subtle" text="{{ __('Start conversation') }}"/>

        <div>
            <flux:button variant="subtle" size="xs" class="w-full">{{ __('Load more') }}</flux:button>
        </div>
    </main>
</div>
