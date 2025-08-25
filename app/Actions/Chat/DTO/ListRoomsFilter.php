<?php

declare(strict_types=1);

namespace App\Actions\Chat\DTO;

use App\Models\Enums\ChatRoomType;
use Illuminate\Support\Str;

final class ListRoomsFilter
{
    public function __construct(
        public readonly int $userId,
        public readonly ?ChatRoomType $type = null,
        private(set) string $keyword = '' {
            set(?string $value) => Str::trim($value ?? '');
        },
    ) {}
}
