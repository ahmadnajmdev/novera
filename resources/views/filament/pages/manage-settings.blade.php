<x-filament-panels::page>
    <form wire:submit="save" class="fi-form grid gap-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save changes</span>
                <span wire:loading wire:target="save">Saving…</span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
