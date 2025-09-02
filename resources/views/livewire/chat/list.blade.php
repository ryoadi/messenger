<?php

use Livewire\Volt\Component;
use App\Actions\Chat\GetRooms;
use App\Actions\Chat\DTO\ListRoomsFilter;
use App\Models\Enums\ChatRoomType;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    // Sidebar filters
    public string $search = '';
    public string $filter = 'all'; // all|direct|group

    public function getRoomsProperty(GetRooms $action)
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
        <livewire:chat.create-room/>
    </flux:modal>

    <div class="overflow-y-auto">
        <flux:navlist>
            @foreach ($this->rooms as $room)
                <flux:navlist.item wire:key="room-{{ $room->id }}"
                                    :href="route('chat.show', ['ChatRoom' => $room])"
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
