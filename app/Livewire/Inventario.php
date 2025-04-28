<?php

namespace App\Livewire;

use App\Models\PcLib;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Flux\Flux;

class Inventario extends Component
{
    #[Validate(['required', 'numeric'], as: 'filial')]
    public $codfilial;
    #[Validate(['required'], as: 'data')]
    public array $range;
    public $sql;
    public $modalsql;
    public $inventarios = [];

    public function mount()
    {
        $this->range = [
            'start' => today()->subDay(7)->format('Y-m-d'),
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

        $dtinicial = $this->range['start'];
        $dtfinal = $this->range['end'];

        $this->sql = DB::connection('oracle')->select("
            SELECT DISTINCT 
              CODFILIAL,
              NUMINVENT,
              DATA,
              MAX(NVL(DATACONT1, DATACONT3)) AS DT_CONTAGEM,
              MAX((SELECT E.NOME_GUERRA FROM PCEMPR E WHERE E.MATRICULA = T.CODFUNCMONTAGEM)) AS CODFUNC
            FROM PCINVENTROT T
            WHERE DTATUALIZACAO IS NULL
              AND DTCANCEL IS NULL
              AND T.CODFILIAL = $this->codfilial
              AND T.DATA BETWEEN '$dtinicial' AND '$dtfinal'
            GROUP BY CODFILIAL,NUMINVENT,DATA
            ORDER BY NUMINVENT DESC
");
    }

    public function modal_juntar()
    {
        if (empty($this->inventarios) or count($this->inventarios) < 2) {
            Flux::toast(
                heading: 'Atenção',
                text: 'Por favor, selecione ao menos dois inventário para prosseguir.',
                variant: 'danger',
            );

            return;
        }

        $inventarios = implode(',', $this->inventarios);

        $sql = "
                BEGIN
                    DVP_PKG.JUNTA_INVENTARIOS(SYS.ODCINUMBERLIST($inventarios));
                END;
                ";

        DB::connection('oracle')->statement($sql);

        $this->reset(['inventarios']);

        $this->modalsql = DB::connection('oracle')->select("
            SELECT DISTINCT
              NUMINVENT,
              RESULTADO,
              QUANT,
              TRUNC(DATA) AS DATA
            FROM DVP_LOGINVENTARIO
            WHERE ANTERIOR IN ($inventarios)
        ");

        Flux::modal('modal')->show();

        Flux::toast(
            heading: 'Sucesso',
            text: 'Inventários mesclados com sucesso.',
            variant: 'success',
        );
    }


    public function modal_separar()
    {
        if (empty($this->inventarios)) {
            Flux::toast(
                heading: 'Atenção',
                text: 'Por favor, selecione ao menos um inventário para prosseguir.',
                variant: 'danger',
            );

            return;
        }

        $inventarios = implode(',', $this->inventarios);

        $sql = "
                BEGIN
                    DVP_PKG.SEPARA_INVENTARIOS(SYS.ODCINUMBERLIST($inventarios));
                END;
                ";

        DB::connection('oracle')->statement($sql);

        $this->reset(['inventarios']);

        $this->modalsql = DB::connection('oracle')->select("
            SELECT DISTINCT
              NUMINVENT,
              RESULTADO,
              QUANT,
              TRUNC(DATA) AS DATA
            FROM DVP_LOGINVENTARIO
            WHERE ANTERIOR IN ($inventarios)
        ");

        Flux::modal('modal')->show();

        Flux::toast(
            heading: 'Sucesso',
            text: 'Inventários separados com sucesso.',
            variant: 'success',
        );
    }

    public function modal_analisar()
    {
        if (empty($this->inventarios)) {
            Flux::toast(
                heading: 'Atenção',
                text: 'Por favor, selecione ao menos um inventário para prosseguir.',
                variant: 'danger',
            );

            return;
        }

        $inventarios = implode(',', $this->inventarios);

        $sql = "
                BEGIN
                    DVP_PKG.ATUALIZA_ANALISE(SYS.ODCINUMBERLIST($inventarios));
                END;
                ";

        DB::connection('oracle')->statement($sql);

        $this->reset(['inventarios']);

        $this->modalsql = DB::connection('oracle')->select("
            SELECT DISTINCT
              NUMINVENT,
              RESULTADO,
              QUANT,
              TRUNC(DATA) AS DATA
            FROM DVP_LOGINVENTARIO
            WHERE ANTERIOR IN ($inventarios)
        ");

        Flux::modal('modal')->show();

        Flux::toast(
            heading: 'Sucesso',
            text: 'Inventários em analise.',
            variant: 'success',
        );
    }

    public function fecharModal()
    {
        $this->submit();

        $this->modal('modal')->close();
    }

    public function render()
    {
        return view('livewire.inventario');
    }
}
