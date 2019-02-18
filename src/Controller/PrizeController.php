<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PrizeService;
use App\Service\ServiceException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("prizes")
 */
class PrizeController extends AbstractController
{
    /**
     * @var PrizeService
     */
    private $prizeService;

    public function __construct(PrizeService $prizeService)
    {
        $this->prizeService = $prizeService;
    }

    /**
     * @Route("/", name="prize_create", methods={"POST"})
     */
    public function create()
    {
        $user = $this->getUser();

        try {
            $prize = $this->prizeService->create($user);
        } catch (ServiceException $e) {
            return $this->json('Internal error', 500);
        }

        return $this->json($prize);
    }

    /**
     * @Route("/{prizeId}", name="prize_update", methods={"PUT"})
     */
    public function update(int $prizeId, Request $request)
    {
        $user = $this->getUser();

        $body = json_decode($request->getContent(), true);

        if (!isset($body['status'])) {
            throw new BadRequestHttpException();
        }

        try {
            $prize = $this->prizeService
                ->update($user, $prizeId, $body['status']);
        } catch (ServiceException $e) {
            return $this->json('Internal error', 500);
        }

        return $this->json($prize);
    }
}