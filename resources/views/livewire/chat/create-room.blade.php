<?php

use App\Actions\Chat\CreateRoom\GetUsers;
use App\Actions\Chat\DTO\CreateGroupRoomInput;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\Volt\Component;
use App\Actions\Chat\CreateRoom\CreateDirectRoom;
use App\Actions\Chat\CreateRoom\CreateGroupRoom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

new class extends Component {

    public string $name = '';
    public string $keyword = '';
    /** @var array<int, string> user id => name */
    public array $selectedUsers = [];

    public function getUsersProperty(GetUsers $query): Collection
    {
        return $query($this->keyword, ...array_keys($this->selectedUsers));
    }

    public function selectUser(int $id, string $name): void
    {
        if ($id === (int) Auth::id()) {
            return; // cannot select self
        }

        $this->selectedUsers[$id] = $name;
    }

    public function removeSelectedUser(int $userId): void
    {
        unset($this->selectedUsers[$userId]);
    }

    public function create(CreateDirectRoom $createDirectRoom, CreateGroupRoom $createGroupRoom): Redirector
    {
        $currentId = (int) Auth::id();
        $room      = count($this->selectedUsers) === 1
            ? $createDirectRoom($currentId, array_key_first($this->selectedUsers))
            : $createGroupRoom(new CreateGroupRoomInput(
                $this->name,
                $currentId,
                collect($this->selectedUsers)->keys(),
            ));

        // Reset and redirect
        $this->reset(['name', 'keyword', 'selectedUsers']);

        return redirect()->route('chat.show', ['chatRoom' => $room]);
    }

}; ?>

<div class="flex flex-col h-full gap-4">
    <flux:heading size="lg">{{ __('Create a new room') }}</flux:heading>

    <div class="space-y-2">
        <flux:button type="button" variant="primary" size="sm" class="w-full"
                     wire:click="create"
                     wire:loading.attr="disabled">
            {{ __('Create') }}
        </flux:button>

        <flux:input size="sm" placeholder="{{ __('Room name') }}"
                    :disabled="count($selectedUsers) <= 1"
                    wire:model.live="name"/>

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

        <flux:button variant="subtle" size="sm" class="w-full mt-2"
                     wire:loading.attr="disabled">{{ __('Load more') }}</flux:button>
    </div>
</div>
