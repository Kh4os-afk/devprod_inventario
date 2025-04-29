<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Index extends Component
{
    #[Computed]
    public function fechados()
    {
        return DB::connection('oracle')->select("
                SELECT COUNT (DISTINCT (NUMINVENT)) QT,
                     CASE
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 0 AND 2   THEN  '1:  0 a  4'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 5 AND 10  THEN  '2:  5 a 10'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 11 AND 20 THEN  '3: 11 a 20'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 21 AND 30 THEN  '4: 21 a 30'
                        ELSE '5: + de 30'
                     END
                        DIAS
                FROM PCINVENTROT
               WHERE     DTATUALIZACAO IS NOT NULL
                     AND DTCANCEL IS NULL
                     AND DTATUALIZACAO > SYSDATE - 60
            GROUP BY CASE
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 0 AND 2   THEN  '1:  0 a  4'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 5 AND 10  THEN  '2:  5 a 10'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 11 AND 20 THEN  '3: 11 a 20'
                        WHEN TRUNC (DTATUALIZACAO - DATA) BETWEEN 21 AND 30 THEN  '4: 21 a 30'
                        ELSE '5: + de 30'
                     END
            ORDER BY DIAS
        ");
    }

    #[Computed]
    public function abertos()
    {
        return DB::connection('oracle')->select("
                 SELECT COUNT (DISTINCT (NUMINVENT)) QT,
                     CASE
                        WHEN TRUNC (sysdate - DATA) BETWEEN 0 AND 2   THEN  '1:  0 a  4'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 5 AND 10  THEN  '2:  5 a 10'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 11 AND 20 THEN  '3: 11 a 20'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 21 AND 30 THEN  '4: 21 a 30'
                        ELSE '5: + de 30'
                     END
                        DIAS
                FROM PCINVENTROT
               WHERE    DTATUALIZACAO IS NULL
                     AND DTCANCEL IS NULL
                   ---  AND sysdate > SYSDATE - 60
            GROUP BY CASE
                        WHEN TRUNC (sysdate - DATA) BETWEEN 0 AND 2   THEN  '1:  0 a  4'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 5 AND 10  THEN  '2:  5 a 10'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 11 AND 20 THEN  '3: 11 a 20'
                        WHEN TRUNC (sysdate - DATA) BETWEEN 21 AND 30 THEN  '4: 21 a 30'
                        ELSE '5: + de 30'
                     END
            ORDER BY DIAS
        ");
    }

    public function render()
    {
        return view('livewire.index');
    }
}
