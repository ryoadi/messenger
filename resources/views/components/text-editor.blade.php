<div
    x-data="setupEditor(
        $wire.entangle('{{ $attributes->wire('model')->value() }}')
    )"
    x-init="() => init($refs.editor)"
    wire:ignore
    {{ $attributes->whereDoesntStartWith('wire:model') }}
>
    <flux:text x-ref="editor" class="*:outline-none"></flux:text>
</div>
