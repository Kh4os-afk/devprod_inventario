<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1" class="animate__animated animate__fadeIn animate__faster">Inventário de Produtos</flux:heading>
        <flux:text class="mb-6 mt-2 text-base animate__animated animate__fadeIn animate__fast">Use os campos abaixo para refinar sua busca.</flux:text>
    </div>
    <flux:separator variant="subtle"/>
    <form wire:submit="submit()" class="flex justify-center animate__animated animate__fadeIn">
        <flux:card class="space-y-6 w-full">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-5">
                    <flux:select variant="listbox" label="Filial" searchable placeholder="Filiais..." wire:model="codfilial" clearable>
                        @foreach($this->filiais as $filial)
                            <flux:select.option value="{{ $filial->codigoa }}">{{ $filial->codigoa }} - {{ ucwords(strtolower($filial->contato)) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="col-span-5">
                    <flux:date-picker label="Data" mode="range" wire:model="range"/>
                </div>

                <flux:field class="col-span-2">
                    <flux:label class="!invisible">Buscar</flux:label>

                    <flux:button icon="magnifying-glass" variant="primary" type="submit" class="w-full">
                        <span class="max-lg:hidden">Buscar</span>
                    </flux:button>
                </flux:field>
            </div>
        </flux:card>
    </form>

    @if($sql)
        <flux:card class="w-full min-w-6/12 animate__animated animate__fadeInUp animate__faster">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Inventários</flux:heading>
                    <flux:text class="mt-2">Selecione um ou mais inventários para realizar as operações desejadas.</flux:text>
                </div>

                <div>
                    <flux:checkbox.group wire:model="inventarios">
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column></flux:table.column>
                                <flux:table.column>Filial</flux:table.column>
                                <flux:table.column>Inventario</flux:table.column>
                                <flux:table.column>Seção</flux:table.column>
                                <flux:table.column>Data</flux:table.column>
                                <flux:table.column>Dias</flux:table.column>
                                <flux:table.column>Contagem</flux:table.column>
                                <flux:table.column>Funcionario</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @forelse($sql ?? [] as $dado)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <flux:checkbox value="{{ $dado->numinvent }}"/>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $dado->filial }}</flux:table.cell>
                                        <flux:table.cell>{{ $dado->numinvent }}</flux:table.cell>
                                        <flux:table.cell>{{ $dado->secao }}</flux:table.cell>
                                        <flux:table.cell>{{ $dado->data ? \Carbon\Carbon::parse($dado->data)->format('d/m/Y') : '' }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :color="($dado->dias <= 5) ? 'lime' : 'red'">{{ $dado->dias }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $dado->dt_contagem ? \Carbon\Carbon::parse($dado->dt_contagem)->format('d/m/Y') : '' }}</flux:table.cell>
                                        <flux:table.cell>{{ $dado->func }}</flux:table.cell>
                                    </flux:table.row>
                                @empty
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </flux:checkbox.group>
                </div>

                <div class="flex justify-items-start gap-2">
                    <flux:spacer/>
                    <flux:button variant="primary" icon="arrows-pointing-in" wire:click="modal_juntar()">Juntar</flux:button>
                    <flux:button variant="primary" icon="arrows-pointing-out" wire:click="modal_separar()">Separar</flux:button>
                    <flux:button variant="primary" icon="cursor-arrow-ripple" wire:click="modal_analisar()">Analisar</flux:button>
                </div>
            </div>
        </flux:card>
    @else
    @endif

    <flux:modal name="modal" class="w-full ">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Ultimas Alterações</flux:heading>
            </div>

            <div>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Inventario</flux:table.column>
                        <flux:table.column>Resultado</flux:table.column>
                        <flux:table.column>Qtde</flux:table.column>
                        <flux:table.column>Data</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse($this->modalsql ?? [] as $dadosmodal)
                            <flux:table.row>
                                <flux:table.cell>{{ $dadosmodal->numinvent }}</flux:table.cell>
                                <flux:table.cell>{{ $dadosmodal->resultado }}</flux:table.cell>
                                <flux:table.cell>{{ $dadosmodal->quant }}</flux:table.cell>
                                <flux:table.cell>{{ $dadosmodal->data ? \Carbon\Carbon::parse($dadosmodal->data)->format('d/m/Y') : '' }}</flux:table.cell>
                            </flux:table.row>
                        @empty
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <div class="flex">
                <flux:spacer/>
                <flux:button type="submit" variant="primary" wire:click="fecharModal()">Fechar</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
