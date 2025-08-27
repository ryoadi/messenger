<?php

namespace App\Actions\Chat\DTO;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateGroupRoomInput
{
    public function __construct(
        public private(set) string $name {
            set => Str::trim($value);
        },
        public readonly int $currentUserId,
        public private(set) Collection $selectedUserIds {
            set => $value
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->reject(fn (int $id) => $id === $this->currentUserId)
                ->values();
        },
    ) {}
}
