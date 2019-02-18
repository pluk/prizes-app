<?php

namespace App\Repository;

use App\Entity\Prize;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Prize|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prize|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prize[]    findAll()
 * @method Prize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrizeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Prize::class);
    }

    public function findAllPrizesPerDay(User $user, string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->andWhere('p.createdDate > :today')
            ->setParameter('today', new \DateTime('today'))
            ->getQuery()
            ->getResult();
    }

    public function findNotFinishedMoneyPrizes(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', Prize::TYPE_MONEY)
            ->andWhere('p.isFinished = :isFinished')
            ->setParameter('isFinished', false)
            ->andWhere('p.status = :status')
            ->setParameter('status', Prize::STATUS_ACCEPTED)
            ->getQuery()
            ->getResult();
    }
}
