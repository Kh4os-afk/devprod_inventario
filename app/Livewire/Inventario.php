<?php

namespace App\Livewire;

use App\Models\PcLib;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Inventario extends Component
{
    #[Validate(['required', 'numeric'], as: 'filial')]
    public $codfilial;
    #[Validate(['required'], as: 'data')]
    public array $range;

    public function mount()
    {
        $this->range = [
            'start' => today()->subDay(1)->format('Y-m-d'),
            'end' => today()->format('Y-m-d'),
        ];
    }

    #[Computed]
    public function filiais()
    {
        return PcLib::where('codfunc', /*auth()->user()->matricula*/ 5601)
            ->join('pcfilial', 'pclib.codigoa', '=', 'pcfilial.codigo')
            ->where('codtabela', 1)
            ->selectRaw("to_char(pclib.codigoa,'00') as codigoa, pcfilial.contato")
            ->orderByRaw("to_char(pclib.codigoa,'00')")
            ->get();
    }

    public function submit()
    {
        $this->validate();

        //Fazer alguma coisa
    }

    public function render()
    {
        return view('livewire.inventario');
    }
}
