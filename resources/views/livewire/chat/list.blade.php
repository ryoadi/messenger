<?php

use Livewire\Volt\Component;
use App\Actions\Chat\ListRooms;
use App\Actions\Chat\CreateRoom;
use App\Actions\Chat\DTO\ListRoomsFilter;
use App\Models\Enums\ChatRoomType;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

new class extends Component {

    // Sidebar filters
    public string $search = '';
    public string $filter = 'all'; // all|direct|group

    // Create room modal state
    public string $roomTitle = '';
    public string $userSearch = '';
    /** @var array<int,int> */
    public array $selectedUserIds = [];

    public function getRoomsProperty(ListRooms $action)
    {
        $type = match ($this->filter) {
            'direct' => ChatRoomType::Direct,
            'group' => ChatRoomType::Group,
            default => null,
        };

        return $action(new ListRoomsFilter(
            (int) Auth::id(),
            $type,
            $this->search,
        ));
    }

    public function getUsersProperty(): Collection
    {
        $currentId = (int) Auth::id();

        return User::query()
            ->whereKeyNot($currentId)
            ->when($this->userSearch !== '', function ($q) {
                $term = '%' . $this->userSearch . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term);
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    public function getSelectedUsersProperty(): Collection
    {
        if (empty($this->selectedUserIds)) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $this->selectedUserIds)
            ->orderBy('name')
            ->get();
    }

    public function toggleSelectUser(int $userId): void
    {
        $currentId = (int) Auth::id();
        if ($userId === $currentId) {
            return; // cannot select self
        }

        if (in_array($userId, $this->selectedUserIds, true)) {
            $this->selectedUserIds = array_values(array_filter($this->selectedUserIds, fn ($id) => $id !== $userId));
        } else {
            $this->selectedUserIds[] = $userId;
        }

        // If we are at direct selection (1 user), ensure title is empty
        if (count($this->selectedUserIds) <= 1) {
            $this->roomTitle = '';
        }
    }

    public function removeSelected(int $userId): void
    {
        $this->selectedUserIds = array_values(array_filter($this->selectedUserIds, fn ($id) => $id !== $userId));
        if (count($this->selectedUserIds) <= 1) {
            $this->roomTitle = '';
        }
    }

    public function create(CreateRoom $createRoom)
    {
        $currentId = (int) Auth::id();

        // Validate basic rules at component level for better UX
        $selected = array_values(array_unique(array_map('intval', $this->selectedUserIds)));
        $selected = array_values(array_filter($selected, fn ($id) => $id !== $currentId));

        if (count($selected) < 1) {
            throw ValidationException::withMessages([
                'users' => __('Please select at least one user.'),
            ]);
        }

        if (count($selected) >= 2 && trim($this->roomTitle) === '') {
            throw ValidationException::withMessages([
                'name' => __('Group chat room title is required.'),
            ]);
        }

        if (count($selected) === 1) {
            $this->roomTitle = '';
        }

        $room = $createRoom($currentId, $selected, $this->roomTitle);

        // Reset and redirect
        $this->reset(['roomTitle', 'userSearch', 'selectedUserIds']);

        return redirect()->route('chat.show', $room);
    }

}; ?>

<div class="flex flex-col gap-2 h-full overflow-y-auto pr-2">
    <div class="py-1 space-y-2 pl-1">
        <flux:modal.trigger name="room-create">
            <flux:button size="sm" class="w-full mb-2">{{ __('New') }}</flux:button>
        </flux:modal.trigger>
        <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" wire:model.live.debounce.300ms="search"/>
        <div class="flex gap-2 justify-center">
            <flux:button size="xs" :variant="$filter === 'all' ? 'primary' : 'ghost'"
                         wire:click="$set('filter','all')">{{ __('All') }}</flux:button>
            <flux:button size="xs" :variant="$filter === 'direct' ? 'primary' : 'ghost'"
                         wire:click="$set('filter','direct')">{{ __('Direct') }}</flux:button>
            <flux:button size="xs" :variant="$filter === 'group' ? 'primary' : 'ghost'"
                         wire:click="$set('filter','group')">{{ __('Group') }}</flux:button>
        </div>
    </div>

    <flux:modal name="room-create" variant="flyout" position="left" class="h-dvh max-w-full md:max-w-1/4">
        <div class="flex flex-col h-full gap-4">
            <flux:heading size="lg">{{ __('Create a new room') }}</flux:heading>

            <div class="space-y-2">
                <flux:button type="button" variant="primary" size="sm" class="w-full"
                             wire:click="create"
                             wire:loading.attr="disabled">
                    {{ __('Create') }}
                </flux:button>

                <flux:input size="sm" placeholder="{{ __('Room name') }}"
                            :disabled="count($selectedUserIds) <= 1"
                            wire:model.live="roomTitle"/>

                <div class="flex gap-2 flex-wrap">
                    @foreach ($this->selectedUsers as $user)
                        <flux:badge variant="pill" size="sm">
                            {{ $user->name }}
                            <flux:badge.close wire:click="removeSelected({{ $user->id }})" />
                        </flux:badge>
                    @endforeach
                </div>

                <flux:input size="sm" type="search" placeholder="{{ __('Search users') }}"
                            wire:model.live.debounce.300ms="userSearch"/>
            </div>

            <div class="overflow-y-auto space-y-1">
                @foreach ($this->users as $user)
                    <flux:button size="sm"
                                 :variant="in_array($user->id, $selectedUserIds, true) ? 'primary' : 'ghost'"
                                 class="w-full gap-2 justify-start"
                                 wire:click="toggleSelectUser({{ $user->id }})">
                        <flux:avatar size="xs" name="{{ $user->name }}"/>
                        <span class="truncate">{{ $user->name }}</span>
                    </flux:button>
                @endforeach

                <flux:button variant="subtle" size="sm" class="w-full mt-2" wire:loading.attr="disabled">{{ __('Load more') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <div class="overflow-y-auto">
        <flux:navlist>
            @foreach ($this->rooms as $room)
                <flux:navlist.item wire:key="room-{{ $room->id }}"
                                   class="[&>[data-content]]:flex [&>[data-content]]:flex-column [&>[data-content]]:items-center [&>[data-content]]:gap-2">
                    <flux:avatar size="xs" name="{{ $room->title }}"/>
                    {{ $room->title }}
                </flux:navlist.item>
            @endforeach

                <flux:button variant="subtle" size="sm" class="w-full mt-2"
                             wire:loading.attr="disabled">{{ __('Load more') }}</flux:button>
        </flux:navlist>
    </div>

</div>
