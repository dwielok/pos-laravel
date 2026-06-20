<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case EWallet = 'e_wallet';
    case StoreCredit = 'store_credit';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Card => 'Card',
            self::BankTransfer => 'Bank Transfer',
            self::EWallet => 'E-Wallet',
            self::StoreCredit => 'Store Credit',
            self::Other => 'Other',
        };
    }
}
