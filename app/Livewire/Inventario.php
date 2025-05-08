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
    public $infoInventarios;

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
        return PcLib::where('codfunc', auth()->user()->matricula)
            ->join('pcfilial', 'pclib.codigoa', '=', 'pcfilial.codigo')
            ->where('codtabela', 1)
            ->selectRaw("to_char(pclib.codigoa,'00') as codigoa, pcfilial.contato")
            ->orderByRaw("to_char(pclib.codigoa,'00')")
            ->get();
    }

    public function submit()
    {
        $this->validate();

        $this->reset(['inventarios']);

        $dtinicial = $this->range['start'];
        $dtfinal = $this->range['end'];

        $this->sql = DB::connection('oracle')->select("
            SELECT DISTINCT
                   TO_CHAR (T.CODFILIAL, '00') FILIAL,
                   NUMINVENT,
                   MAX (S.DESCRICAO) || ' (' || COUNT (DISTINCT (T.CODPROD)) || ')' SECAO,
                   MIN (DATA) AS DATA,
                   TRUNC (MIN (SYSDATE - DATA)) AS DIAS,
                   MAX (TRUNC (NVL (DATACONT1, DATACONT3))) AS DT_CONTAGEM,
                   MAX ( (SELECT E.NOME_GUERRA
                            FROM PCEMPR E
                           WHERE E.MATRICULA = T.CODFUNCMONTAGEM))
                       AS FUNC
              FROM PCINVENTROT T, PCPRODUT P, PCSECAO S
             WHERE     1 = 1
                   AND P.CODSEC = S.CODSEC
                   AND P.CODEPTO = S.CODEPTO
                   AND P.CODPROD = T.CODPROD
                   AND DTATUALIZACAO IS NULL
                   AND DTCANCEL IS NULL
                   AND T.CODFILIAL = $this->codfilial
                   AND T.DATA BETWEEN '$dtinicial' AND '$dtfinal'
            GROUP BY T.CODFILIAL, NUMINVENT
            ORDER BY DATA
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

        $this->submit();

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

        $this->submit();

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

        $this->submit();

        Flux::modal('modal')->show();

        Flux::toast(
            heading: 'Sucesso',
            text: 'Inventários em analise.',
            variant: 'success',
        );
    }

    public function infoInventario($numinvent)
    {
        $this->infoInventarios = DB::connection('oracle')->select("
            SELECT I.CODPROD,
                 P.DESCRICAO || ' ' || P.EMBALAGEM AS descricao,
                 NVL (I.QT1, 0) AS QT1,
                 I.QT2,
                 I.QTESTGER ESTOQUE,
                 (NVL (I.QT1, 0) - I.QTESTGER) DIF,
                 TRUNC (NVL (E.CUSTOFIN, E.CUSTOULTENT), 2) CUSTO,
                 C.CATEGORIA || ' ' || C.CODCATEGORIA AS CATEGORIA,
                 TO_CHAR (E.DTULTSAIDA, 'DD/MM/YY') ULTSAIDA,
                 TO_CHAR (E.DTULTENT, 'DD/MM/YY') ULTENTRA,
                 CASE
                    WHEN P.DTEXCLUSAO IS NOT NULL THEN 'S'
                    ELSE DECODE (P.OBS2, 'FL', 'S', F.FORALINHA)
                 END
                    AS F_LINHA,
                 E.QTESTGER EST_ATUAL,
                 CASE
                    WHEN NVL (I.QT1, 0) = 0 AND NVL (I.QTESTGER, 0) = 0
                    THEN
                       'Não Encontrado'
                    WHEN NVL (I.QT1, 0) > NVL (I.QTESTGER, 0)
                    THEN
                       'Sobrando'
                    WHEN NVL (I.QT1, 0) < NVL (I.QTESTGER, 0)
                    THEN
                       'Faltando'
                    ELSE
                       'Igual'
                 END
                    AS RESULTADO
            FROM PCINVENTROT I,
                 PCPRODUT P,
                 PCCATEGORIA C,
                 PCEST E,
                 PCPRODFILIAL F
           WHERE     I.NUMINVENT = $numinvent
                 AND E.CODPROD = P.CODPROD
                 AND E.CODFILIAL = I.CODFILIAL
                 AND F.CODFILIAL = E.CODFILIAL
                 AND F.CODPROD = E.CODPROD
                 AND I.CODPROD = P.CODPROD
                 AND C.CODCATEGORIA = P.CODCATEGORIA
                 -- AND C.CODCATEGORIA = :CODCATEGORIA
                 -- AND P.CODPROD = :CODPROD
                 AND C.CODSEC = P.CODSEC
                 AND NOT (    NVL (I.QT1, 0) = 0
                          AND I.QT2 = 0
                          AND NVL (I.QTESTGER, 0) = 0
                          AND NVL (E.QTESTGER, 0) = 0
                          AND (CASE
                                  WHEN P.DTEXCLUSAO IS NOT NULL THEN 'S'
                                  ELSE DECODE (P.OBS2, 'FL', 'S', F.FORALINHA)
                               END) = 'S')
        ORDER BY C.CODCATEGORIA, P.DESCRICAO
        ");

        Flux::modal('info-inventario')->show();
    }

    public function render()
    {
        return view('livewire.inventario');
    }
}
