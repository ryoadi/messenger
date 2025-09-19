<?php

use App\Actions\Chat\GroupRoom\AddMembers;
use App\Actions\Chat\GroupRoom\GetUserCandidates;
use App\Models\ChatRoom;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {

    #[Locked]
    public ChatRoom $room;

    public string $name = '';
    public string $keyword = '';
    /** @var array<int, string> user id => name */
    public array $selectedUsers = [];

    public function getUsersProperty(GetUserCandidates $query): Collection
    {
        return $query($this->room, $this->keyword, ...array_keys($this->selectedUsers));
    }

    public function selectUser(int $id, string $name): void
    {
        $this->selectedUsers[$id] = $name;
    }

    public function removeSelectedUser(int $userId): void
    {
        unset($this->selectedUsers[$userId]);
    }

    public function addMembers(AddMembers $add): void
    {
        $this->authorize('manage', $this->room);

        $this->validate([
            'selectedUsers' => ['required', 'array', 'min:1'],
        ]);

        $add($this->room, ...array_keys($this->selectedUsers));

        $this->redirectRoute(
            'chat.show', 
            ['chatRoom' => $this->room],
            navigate: true,
        );

        $this->reset(
            'name',
            'keyword',
            'selectedUsers',
        );

    }

}; ?>

<div class="flex flex-col h-full gap-4 max-w-sm">
    <flux:heading size="lg">{{ __('Add new members') }}</flux:heading>

    <div class="space-y-2">
        <flux:button type="button" variant="primary" size="sm" class="w-full"
                     wire:click="addMembers"
                     wire:loading.attr="disabled">
            {{ __('Add') }}
        </flux:button>

        <div class="flex gap-2 flex-wrap">
            @foreach ($this->selectedUsers as $id => $name)
                <flux:badge variant="pill" size="sm">
                    {{ $name }}
                    <flux:badge.close wire:click="removeSelectedUser({{ $id }})"/>
                </flux:badge>
            @endforeach
        </div>

        <flux:input size="sm" type="search" placeholder="{{ __('Search users') }}"
                    wire:model.live.debounce.300ms="keyword"/>
    </div>

    <div class="overflow-y-auto space-y-1">
        @foreach ($this->users as $user)
            <flux:button size="sm"
                         class="w-full gap-2 justify-start"
                         wire:click="selectUser({{ $user->id }}, '{{$user->name}}')">
                <span class="truncate">{{ $user->name }}</span>
            </flux:button>
        @endforeach

        <flux:button variant="subtle" size="sm" class="w-full mt-2 max-w-md"
                     wire:loading.attr="disabled">{{ __('Load more') }}</flux:button>
    </div>
</div>
