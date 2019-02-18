<?php

namespace App\Service;


use App\Repository\PrizeRepository;

class PrizeService
{
    /**
     * @var PrizeRepository
     */
    private $prizeRepository;

    public function __construct(PrizeRepository $prizeRepository)
    {
        $this->prizeRepository = $prizeRepository;
    }
}