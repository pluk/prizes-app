<?php
/**
 * Created by PhpStorm.
 * User: pluk
 * Date: 17.02.19
 * Time: 11:45
 */

namespace App\Service;


use App\Entity\Prize;
use App\Entity\User;
use App\Repository\PrizeRepository;

class MoneySpecification
{
    public const MONEY_PRIZES_LIMIT = 3;

    /**
     * @var PrizeRepository
     */
    private $prizeRepository;

    public function __construct(PrizeRepository $prizeRepository)
    {
        $this->prizeRepository = $prizeRepository;
    }

    public function isSatisfiedBy(User $user): bool
    {
        $moneyPrizesPerDay = $this
            ->prizeRepository
            ->findAllPrizesPerDay($user, Prize::TYPE_MONEY);

        return count($moneyPrizesPerDay) < self::MONEY_PRIZES_LIMIT;
    }
}