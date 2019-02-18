<?php

namespace App\Event;

use App\Entity\Prize;
use Symfony\Component\EventDispatcher\Event;

class PrizeAcceptedEvent extends Event
{
    const NAME = 'prize.accepted';
    /**
     * @var Prize
     */
    private $prize;


    public function __construct(Prize $prize)
    {
        $this->prize = $prize;
    }

    public function getPrize(): Prize
    {
        return $this->prize;
    }
}