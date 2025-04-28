<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">Inventário de Produtos</flux:heading>
        <flux:text class="mb-6 mt-2 text-base">Use os campos abaixo para refinar sua busca.</flux:text>
    </div>
    <flux:separator variant="subtle"/>
    <form wire:submit="submit()" class="flex justify-center">
        <flux:card class="space-y-6 w-full max-w-5xl">
            <div>
                <flux:heading size="lg">Dados do Inventário</flux:heading>
            </div>

            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-6">
                    <flux:select variant="listbox" label="Filial" searchable placeholder="Filiais..." wire:model="codfilial" clearable>
                        @foreach($this->filiais as $filial)
                            <flux:select.option value="{{ $filial->codigoa }}">{{ $filial->codigoa }} - {{ ucwords(strtolower($filial->contato)) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="col-span-6">
                    <flux:date-picker label="Data" mode="range" wire:model="range"/>
                </div>
            </div>

            <div>
                <flux:button icon="check" variant="primary" type="submit">Buscar</flux:button>
                <flux:button icon="x-mark">Limpar</flux:button>
            </div>
        </flux:card>
    </form>
</div>
