<x-filament::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}
        <div class="pt-4">
            <x-filament::button type="submit" color="warning">
                Submit Ticket
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
