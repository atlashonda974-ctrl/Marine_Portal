<?php

namespace App\Helpers;

class BankHelper
{
    private static $bankList = [
        '12539' => 'Al Baraka Islamic Bank Ltd',
        '1150Y' => 'Askari bank Limited Islamic Banking Services',
        '11555' => 'Askari Commercial Bank Ltd',
        '11510' => 'Askari bank Limited',
        '115AS' => 'Askari Islamic Bank Limited',
        '20603' => 'Askari Leasing Ltd',
        '12503' => 'Al-Baraka Islamic Bank',
        '14401' => 'Al-Faysal Investment Bank Ltd',
        '10116' => 'Allied Bank Limited',
        '101CA' => 'Allied Bank Limited (Islamic Banking)',
        '114FH' => 'Bank Al-Falah Ltd (Islamic)',
        '11410' => 'Bank Al-Falah Limited',
        '11369' => 'Bank Al Habib (Islamic)',
        '11302' => 'Bank Al Habib Limited',
        '16002' => 'Bank Islami Pakistan Limited',
        '61601' => 'Bank Makramah Limited',
        '12801' => 'Dubai Islamic Bank ltd',
        '15307' => 'Dubai Islamic Bank Pakistan ltd',
        '18601' => 'FINCA Microfinance Bank Limited',
        '18601' => 'Faysal Bank Limited (Islamic)', // Note: Duplicate code
        '11101' => 'Faysal Bank Limited',
        '13201' => 'Faysal Islamic Bank Limited',
        '31009' => 'First Habib Bank Modaraba',
        '30603' => 'First National Bank Modaraba',
        '13510' => 'First Women Bank Limited',
        '17101' => 'HSBC Bank Middle East Ltd',
        '10801' => 'Habib Bank Limited',
        '15271' => 'Habib Metro Bank Limited',
        '15205' => 'Habib Metropolitan Bank Limited',
        '16189' => 'J.S Bank Limited',
        '10416' => 'MCB Bank Limited',
        '104VE' => 'MCB Islami Bank Limited',
        '13003' => 'Meezan Bank Limited',
        '12003' => 'Metropolitan Bank Limited',
        '18501' => 'Micro Finance Apna Bank Limited',
        '106DT' => 'NBP Islamic',
        '10610' => 'National Bank of Pakistan',
        '11201' => 'SME Bank Limited',
        '22703' => 'Standard Chartered Leasing',
        '17401' => 'Samba Bank Limited',
        '11826' => 'Saudi Pak Commercial Bank Limited',
        '17304' => 'Silk Bank Limited',
        '17903' => 'Sindh Bank Limited',
        '23401' => 'Sindh Leasing Company Limited',
        '12901' => 'Soneri Bank Limited',
        '10304' => 'Standard Chartered LIMITED',
        '17615' => 'Summit Bank Limited',
        '12202' => 'The Bank Of Khyber',
        '134AU' => 'The Bank Of Punjab',
        '18401' => 'U Microfinance Bank Limited',
        '10704' => 'United Bank Limited',
    ];

    public static function getBankDescriptions($bankCodes)
    {
        if (is_array($bankCodes)) {
            $descriptions = [];
            foreach ($bankCodes as $code) {
                $descriptions[$code] = self::$bankList[$code] ?? null;
            }
            return $descriptions;
        }

        // If a single code is passed, return its description directly
        return self::$bankList[$bankCodes] ?? null;
    }
}