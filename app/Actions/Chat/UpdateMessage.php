<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class UpdateMessage
{
    public function __invoke(ChatMessage $message, string $newContent): ChatMessage
    {
        Gate::authorize('manage', $message);

        $message->update(['content' => $newContent]);
        return $message->refresh();
    }
}
