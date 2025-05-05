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
                       WHEN TRUNC (SYSDATE - DATA) BETWEEN 0 AND 2 THEN '1: 0 a 2'
                       WHEN TRUNC (SYSDATE - DATA) BETWEEN 3 AND 5 THEN '2: 3 a 5'
                       WHEN TRUNC (SYSDATE - DATA) BETWEEN 6 AND 10 THEN '3: 6 a 10'
                       WHEN TRUNC (SYSDATE - DATA) BETWEEN 11 AND 20 THEN '4: 11 a 20'
                       WHEN TRUNC (SYSDATE - DATA) BETWEEN 21 AND 30 THEN '5: 21 a 30'
                       ELSE '6: + de 30'
                   END
                       DIAS
              FROM PCINVENTROT
             WHERE     DTATUALIZACAO IS NOT NULL
                   AND DTCANCEL IS NULL
                   AND DTATUALIZACAO > SYSDATE - 60
            GROUP BY CASE
                         WHEN TRUNC (SYSDATE - DATA) BETWEEN 0 AND 2 THEN '1: 0 a 2'
                         WHEN TRUNC (SYSDATE - DATA) BETWEEN 3 AND 5 THEN '2: 3 a 5'
                         WHEN TRUNC (SYSDATE - DATA) BETWEEN 6 AND 10 THEN '3: 6 a 10'
                         WHEN TRUNC (SYSDATE - DATA) BETWEEN 11 AND 20 THEN '4: 11 a 20'
                         WHEN TRUNC (SYSDATE - DATA) BETWEEN 21 AND 30 THEN '5: 21 a 30'
                         ELSE '6: + de 30'
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
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 0 AND 2 THEN '1: 0 a 2'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 3 AND 5 THEN '2: 3 a 5'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 6 AND 10 THEN '3: 6 a 10'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 11 AND 20 THEN '4: 11 a 20'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 21 AND 30 THEN '5: 21 a 30'
                      ELSE '6: + de 30'
                   END
                        DIAS
                FROM PCINVENTROT
               WHERE    DTATUALIZACAO IS NULL
                     AND DTCANCEL IS NULL
                   ---  AND sysdate > SYSDATE - 60
            GROUP BY CASE
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 0 AND 2 THEN '1: 0 a 2'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 3 AND 5 THEN '2: 3 a 5'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 6 AND 10 THEN '3: 6 a 10'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 11 AND 20 THEN '4: 11 a 20'
                      WHEN TRUNC (SYSDATE - DATA) BETWEEN 21 AND 30 THEN '5: 21 a 30'
                      ELSE '6: + de 30'
                   END
            ORDER BY DIAS
        ");
    }

    #[Computed]
    public function analiseFechados()
    {
        return DB::connection('oracle')->select("
                  SELECT to_char (T.CODFILIAL,'00') CODFILIAL,
                         round(  (AVG (  (  T.DTATUALIZACAO
                                   - GREATEST (NVL (T.DATACONT1, DATE '1900-01-01'),
                                               NVL (T.DATACONT2, DATE '1900-01-01'),
                                               NVL (T.DATACONT3, DATE '1900-01-01'),
                                               NVL (T.DATA, DATE '1900-01-01')))
                                * 24)* 60)
                         / COUNT (*),2)
                            HORAS
                    FROM PCINVENTROT T
                   WHERE     T.DTATUALIZACAO IS NOT NULL
                         AND T.DTCANCEL IS NULL
                         AND T.DTATUALIZACAO > SYSDATE - 60
                         AND NOT ( (T.QTESTGER - T.QT1) BETWEEN -1 AND 1)
                GROUP BY to_char (T.CODFILIAL,'00')
                ORDER BY horas
        ");
    }

    #[Computed]
    public function analiseAbertos()
    {
        return DB::connection('oracle')->select("
                   SELECT to_char (T.CODFILIAL,'00') CODFILIAL,
                     round(  (AVG (  (  sysdate
                               - GREATEST (NVL (T.DATACONT1, DATE '1900-01-01'),
                                           NVL (T.DATACONT2, DATE '1900-01-01'),
                                           NVL (T.DATACONT3, DATE '1900-01-01'),
                                           NVL (T.DATA, DATE '1900-01-01')))
                            * 24)* 60)
                     / COUNT (*),2)
                        HORAS
                FROM PCINVENTROT T
               WHERE     T.DTATUALIZACAO IS  NULL
                     AND T.DTCANCEL IS NULL
                     AND NOT ( (T.QTESTGER - T.QT1) BETWEEN -1 AND 1)
            GROUP BY to_char (T.CODFILIAL,'00')
            ORDER BY horas
        ");
    }

    public function render()
    {
        return view('livewire.index');
    }
}
