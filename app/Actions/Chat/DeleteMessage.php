<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Events\MessageDeleted;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Gate;

final class DeleteMessage
{
    public function __invoke(ChatMessage $message): void
    {
        Gate::authorize('manage', $message);

        $message->delete();
    }
}
