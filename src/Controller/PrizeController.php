<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("prize")
 */
class PrizeController
{
    /**
     * @Route("/", name="prize_index")
     */
    public function index()
    {
        return new JsonResponse(['message' => 'Hello world']);
    }
}