<?php

namespace App\Service;


use App\Entity\Prize;
use App\Entity\User;
use App\Event\PrizeAcceptedEvent;
use App\Repository\PrizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PrizeService
{
    /**
     * @var PrizeRepository
     */
    private $prizeRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->prizeRepository = $em->getRepository(Prize::class);
        $this->eventDispatcher = $eventDispatcher;

    }

    public function create(User $user): Prize
    {
        $prizeSpecification = new PrizeSpecification($this->prizeRepository);

        $prizeType = Prize::TYPE_BONUS;
        $allTypes = [Prize::TYPE_MONEY, Prize::TYPE_BONUS, Prize::TYPE_GIFT];
        shuffle($allTypes);

        foreach ($allTypes as $type) {
            if ($prizeSpecification->isSatisfiedBy($user, $type)) {
                $prizeType = $type;
                break;
            }
        }

        try {
            $prize = new Prize($user, $prizeType);

            $this->em->persist($prize);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new ServiceException('Error creating prize', 0, $e);
        }

        return $prize;
    }

    public function update(User $user, int $prizeId, string $status): Prize
    {
        $prize = $this->prizeRepository->find($prizeId);

        if (!$prize) {
            throw new NotFoundHttpException(
                sprintf('Prize with id = %d not found', $prizeId)
            );
        }

        if (!$prize->ensureUserHavePermissions($user)) {
            throw new ServiceException('You are not allowed to edit prize');
        }

        switch ($status) {
            case Prize::STATUS_CANCELED:
                $prize->markAsCanceled();
                break;
            case Prize::STATUS_ACCEPTED:
                #TODO Переписать нормально
                if ($prize->isMoneyPrize()) {
                    $prize->markAsAccepted();
                    $event = new PrizeAcceptedEvent($prize);
                    $this->eventDispatcher
                        ->dispatch(PrizeAcceptedEvent::NAME, $event);
                } elseif ($prize->isGiftPrize()) {
                    #Логика отправки на почту
                    $prize->markAsAccepted();
                    $prize->markAsFinished();
                } elseif ($prize->isBonusPrize()) {
                    $prize->markAsAccepted();
                    $user->creditBonusToAccount($prize->getValue());
                    $prize->markAsFinished();
                }
                break;
            default:
                throw new ServiceException();
        }


        $this->em->flush();

        return $prize;
    }
}