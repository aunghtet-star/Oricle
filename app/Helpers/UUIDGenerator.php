<?php

namespace App\Helpers;

use App\Transaction;
use App\Wallet;

class UUIDGenerator
{
    public static function AccountNumber()
    {
        $number = mt_rand(1000000000000000, 9999999999999999);
        if (Wallet::where('account_numbers', $number)->exists()) {
            self::AccountNumber();
        }
        return $number;
    }
    public static function RefNumber()
    {
        $number = mt_rand(1000000000000000, 9999999999999999);
        if (Transaction::where('ref_no', $number)->exists()) {
            self::RefNumber();
        }
        return $number;
    }
    public static function TrxId()
    {
        $number = mt_rand(1000000000000000, 9999999999999999);
        if (Transaction::where('trx_id', $number)->exists()) {
            self::TrxId();
        }
        return $number;
    }
}
