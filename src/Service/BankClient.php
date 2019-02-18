<?php

namespace App\Service;


use App\Entity\User;

class BankClient
{
    public function creditMoneyToBankAccount(User $user, int $value)
    {

    }

    public function creditMoneyToBankAccountBatch(array $batch): array
    {
        return [];
    }
}