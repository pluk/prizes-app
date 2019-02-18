<?php
/**
 * Created by PhpStorm.
 * User: pluk
 * Date: 17.02.19
 * Time: 11:45
 */

namespace App\Service;


use App\Entity\{Prize, User};
use App\Repository\PrizeRepository;

class PrizeSpecification
{
    /**
     * @var MoneySpecification
     */
    private $moneySpecification;

    /**
     * @var GiftSpecification
     */
    private $giftSpecification;

    public function __construct(PrizeRepository $prizeRepository)
    {
        $this->moneySpecification = new MoneySpecification($prizeRepository);
        $this->giftSpecification = new GiftSpecification($prizeRepository);
    }

    public function isSatisfiedBy(User $user, string $prizeType): bool
    {
        if ($prizeType == Prize::TYPE_MONEY) {
            return $this->moneySpecification->isSatisfiedBy($user);
        }

        if ($prizeType == Prize::TYPE_GIFT) {
            return $this->giftSpecification->isSatisfiedBy($user);
        }

        return true;
    }
}