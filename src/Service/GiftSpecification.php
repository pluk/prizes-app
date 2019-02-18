<?php

namespace App\Service;

use App\Entity\Prize;
use App\Entity\User;
use App\Repository\PrizeRepository;

class GiftSpecification
{
    public const GIFT_PRIZES_LIMIT = 1;

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
        $giftPrizesPerDay = $this->prizeRepository
            ->findAllPrizesPerDay($user, Prize::TYPE_GIFT);

        return count($giftPrizesPerDay) < self::GIFT_PRIZES_LIMIT;
    }
}