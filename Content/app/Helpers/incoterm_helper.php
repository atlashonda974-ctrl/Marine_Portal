<?php

namespace App\Helpers;

class incoterm_helper
{
    function getIncotermDescription(string $code): ?string
    {
        $incoterms = [
            '001' => 'FOB',
            '002' => 'C&F',
            '003' => 'Freight',
            '004' => 'CIF',
            '005' => 'CIP',
            '006' => 'EXW',
            '007' => 'CPT',
            '008' => 'CFR',
            '009' => 'FCA',
            '010' => 'DAP',
            '011' => 'DDP',
            '012' => 'DDU',
        ];

        return $incoterms[$code] ?? null;
    }

}