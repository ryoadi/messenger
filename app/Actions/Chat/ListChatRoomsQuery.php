<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\ChatRoomType;

final class ListChatRoomsQuery
{
    /**
     * @param  int                $userId     The current user ID (required).
     * @param  ChatRoomType|null  $type       null = all, otherwise direct|group.
     * @param  string|null        $search     Search string (optional).
     * @param  int                $perPage    Page size when paginating.
     * @param  bool               $paginate   true => simplePaginate, false => get().
     * @param  array<int, string> $with       Relations to eager-load.
     */
    public function __construct(
        public int $userId,
        public ?ChatRoomType $type = null,
        public ?string $search = null,
        public int $perPage = 20,
        public bool $paginate = true,
        public array $with = ['users:id,name'],
    ) {
    }

    public function trimmedSearch(): string
    {
        return trim((string) $this->search);
    }

    public function hasSearch(): bool
    {
        return $this->trimmedSearch() !== '';
    }
}
