<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Gate;

final class UpdateMessage
{
    public function __invoke(ChatMessage $message, string $rawContent): ChatMessage
    {
        Gate::authorize('manage', $message);

        $validated = validator(
            ['content' => $rawContent],
            ['content' => ['required', 'string']]
        )->validate();

        $newContent = trim((string) $validated['content']);

        if ($newContent !== (string) $message->content) {
            $message->update(['content' => $newContent]);
        }

        return $message->refresh();
    }
}
