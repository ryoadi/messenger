<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $own = false;
    //
}; ?>

<div class="lg:max-w-3/4 px-2 py-2 rounded-md {{ $own ? 'self-end dark:bg-zinc-600' : 'self-start dark:bg-zinc-700'}}">
    <flux:text>the messages</flux:text>
    <flux:text size="sm" variant="subtle" class="text-end"><time>10:00</time></flux:text>
</div>
