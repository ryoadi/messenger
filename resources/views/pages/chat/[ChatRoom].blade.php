<?php

use function Laravel\Folio\{name, middleware};

name('chat.show');
middleware('can:view,chatRoom');
?>

<x-layouts.app :title="__('Chat')">
    <livewire:chat.room :room="$chatRoom" />
</x-layouts.app>
