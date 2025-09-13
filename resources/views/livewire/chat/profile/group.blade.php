<?php

use App\Models\ChatRoom;
use App\Models\Enums\ChatRoomType;
use App\Models\Enums\ChatRoomUserRole;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use App\Actions\Chat\GroupRoom\EditRoom;

new class extends Component {
    #[Locked]
    public ChatRoom $room;

    // local editable title used for server updates
    public string $title = '';

    public bool $editing = false;

    public function mount(): void
    {
        $this->title = (string) $this->room->title;
    }

    public function getUsersProperty()
    {
        return $this->room->users->groupBy(fn($u) => $u->pivot->role);
    }

    public function updateTitle(EditRoom $edit): void
    {
        $this->authorize('manage', $this->room);

        $validated = $this->validate([
            'title' => ['required', 'string'],
        ]);

        $updated = $edit($this->room, $validated['title']);

        // keep current component in sync so UI updates immediately
        $this->room = $updated;

        // also update local title copy
        $this->title = (string) $updated->title;

        // ask the browser to close the edit UI (handled in Alpine)
        $this->editing = false;
    }
}; ?>

<div class="flex flex-col gap-6 items-center">
    <flux:avatar circle :name="$room->title" size="xl"/>

    {{-- Editable title for owners: Alpine toggles edit mode locally --}}
    <div x-data="{ editing: $wire.entangle('editing') }" class="relative group w-full max-w-md">
        <flux:heading size="xl" class="flex items-center">
            <template x-if="!editing">
                @can('manage', $room)
                    <span x-on:click="editing = true" class="cursor-pointer">
                        {{ $room->title }}
                    </span>
                @else
                    <span class="cursor-default">{{ $room->title }}</span>
                @endcan
            </template>

            <template x-if="editing">
                <form class="w-full">
                    <input
                        wire:model="title"
                        wire:keydown.enter.prevent="updateTitle"
                        @keydown.escape.prevent="editing = false"
                        class="w-full bg-white dark:bg-zinc-800 border rounded px-2 py-1"
                        autofocus
                    />
                    {{-- show validation error --}}
                    @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </form>
            </template>

            {{-- edit icon shown on hover to indicate clickability --}}
            @can('manage', $room)
                <button x-show="!editing" x-on:click="editing = true" class="ml-2 opacity-0 group-hover:opacity-100 transition" aria-hidden="true">
                    <flux:icon name="pencil" class="w-4 h-4"/>
                </button>
            @endcan
        </flux:heading>
    </div>

    <div class="w-full space-y-6">
        @if(!empty($this->users['owner']))
            <div class="space-y-2">
                <flux:heading size="sm">{{ __('Owner') }}</flux:heading>
                <div class="flex flex-col">
                    @foreach($this->users['owner'] as $user)
                        <div class="flex items-center justify-between py-2" wire:key="owner-{{ $user->id }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <flux:avatar size="xs" circle :name="$user->name" />
                                <flux:text class="truncate">{{ $user->name }}</flux:text>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($this->users['member']))
            <div class="space-y-2">
                <flux:heading size="sm">{{ __('Members') }}</flux:heading>
                <div class="flex flex-col">
                    @foreach($this->users['member'] as $user)
                        <div class="flex items-center justify-between py-2" wire:key="member-{{ $user->id }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <flux:avatar size="xs" circle :name="$user->name" />
                                <flux:text class="truncate">{{ $user->name }}</flux:text>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
