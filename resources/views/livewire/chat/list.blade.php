<?php

use Livewire\Volt\Component;
use App\Actions\Chat\ListChatRooms;
use App\Actions\Chat\ListChatRoomsQuery;
use App\ChatRoomType;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $search = '';
    public string $filter = 'all'; // all|direct|group
    public int $perPage = 20;

    public function getRoomsProperty()
    {
        $type = match ($this->filter) {
            'direct' => ChatRoomType::Direct,
            'group' => ChatRoomType::Group,
            default => null,
        };

        /** @var ListChatRooms $action */
        $action = app(ListChatRooms::class);

        return $action->handle(new ListChatRoomsQuery(
            userId: (int) Auth::id(),
            type: $type,
            search: $this->search,
            perPage: $this->perPage,
            paginate: true,
            with: ['users:id,name'],
        ));
    }
}; ?>

<div class="flex flex-col gap-2 h-full overflow-y-auto pr-2">
    <div class="py-1 space-y-2 pl-1">
            <flux:modal.trigger name="room-create">
            <flux:button size="sm" class="w-full mb-2">{{ __('New') }}</flux:button>
            </flux:modal.trigger>
            <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" wire:model.live.debounce.300ms="search" />
        <div class="flex gap-2 justify-center">
            <flux:button size="xs" :variant="$filter === 'all' ? 'primary' : 'ghost'" wire:click="$set('filter','all')">{{ __('All') }}</flux:button>
            <flux:button size="xs" :variant="$filter === 'direct' ? 'primary' : 'ghost'" wire:click="$set('filter','direct')">{{ __('Direct') }}</flux:button>
            <flux:button size="xs" :variant="$filter === 'group' ? 'primary' : 'ghost'" wire:click="$set('filter','group')">{{ __('Group') }}</flux:button>
        </div>
    </div>

    <flux:modal name="room-create" variant="flyout" position="left" class="h-dvh max-w-full md:max-w-1/4">
        <div class="flex flex-col h-full gap-4">
            <flux:heading size="lg">{{ __('Create a new room') }}</flux:heading>

            <div class="space-y-2">
                <flux:button type="submit" variant="primary" size="sm" class="w-full">{{ __('Create') }}</flux:button>
                <flux:input size="sm" placeholder="{{ __('Room name') }}" />

                <div class="flex gap-2 flex-wrap">
                    <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                    <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                    <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                    <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                    <flux:badge variant="pill" size="sm" >username<flux:badge.close /></flux:badge>
                </div>

                <flux:input size="sm" type="search" placeholder="{{ __('Search') }}" />
            </div>

            <div class="overflow-y-auto space-y-1">
                @for ($index = 0; $index < 20; $index++)
                    <flux:button size="sm" variant="ghost" class="w-full gap-2 justify-start">
                        <flux:avatar badge badge:color="green" size="xs" name="username" href="#" />
                        username
                    </flux:button>
                @endfor

                <flux:button variant="subtle" size="sm" class="w-full mt-2">{{ __('Load more') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    <div class="overflow-y-auto">
        <flux:navlist>
            @foreach ($this->rooms as $room)
                <flux:navlist.item wire:key="room-{{ $room->id }}" class="[&>[data-content]]:flex [&>[data-content]]:flex-column [&>[data-content]]:items-center [&>[data-content]]:gap-2">
                    <flux:avatar size="xs" name="{{ $room->title }}" />
                    {{ $room->title }}
                </flux:navlist.item>
            @endforeach

            @if ($this->rooms->hasMorePages())
                <flux:button variant="subtle" size="sm" class="w-full mt-2" wire:click="$set('perPage', $perPage + 20)" wire:loading.attr="disabled">{{ __('Load more') }}</flux:button>
            @endif
        </flux:navlist>
    </div>

</div>
