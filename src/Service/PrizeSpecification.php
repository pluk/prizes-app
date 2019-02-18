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
        return $this->prizeRepository->count(
            [
                'user_id' => $user->getId(),
                'type' => Prize::TYPE_GIFT,
                'created_date' => new \DateTime()
            ]
            ) < self::GIFT_PRIZES_LIMIT;
    }
}