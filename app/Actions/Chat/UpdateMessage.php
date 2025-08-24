<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class UpdateMessage
{
    public function __invoke(ChatMessage $message, string $rawContent): ChatMessage
    {
        Gate::authorize('manage', $message);

        $newContent = Str::trim($rawContent);
        if ($newContent === $message->content) {
            return $message;
        }

        validator(
            ['content' => $newContent],
            ['content' => ['required', 'string']]
        )->validate();

        $message->update(['content' => $newContent]);
        return $message->refresh();
    }
}
