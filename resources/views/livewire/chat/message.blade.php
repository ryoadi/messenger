<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $own = false;
    //
}; ?>

<div class="px-4 py-2 max-w-3/4 rounded-md {{ $own ? 'self-end dark:bg-zinc-600' : 'self-start dark:bg-zinc-700'}}">
    <flux:heading>username</flux:heading>
    <flux:text>the messages</flux:text>
</div>
