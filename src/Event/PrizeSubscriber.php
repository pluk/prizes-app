<?php

namespace App\Event;

use App\Service\BankClient;
use App\Service\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PrizeSubscriber implements EventSubscriberInterface
{
    /**
     * @var BankClient
     */
    private $bankClient;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        BankClient $bankClient,
        EntityManagerInterface $em
    ) {
        $this->bankClient = $bankClient;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            PrizeAcceptedEvent::NAME => 'onPrizeAccept'
        ];
    }

    public function onPrizeAccept(PrizeAcceptedEvent $event)
    {
        $prize = $event->getPrize();
        $user = $prize->getUser();

        try {
            $this->bankClient
                ->creditMoneyToBankAccount(
                    $user,
                    $prize->getValue()
                );
        } catch (\Exception $e) {
            throw new ServiceException(
                sprintf(
                    'Не удалось зачислить средства в банк для пользователя %s',
                    $user->getId()
                )
            );
        }

        $prize->markAsFinished();
        $this->em->flush();
    }
}