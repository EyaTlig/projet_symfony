<?php
// src/Repository/SubscriptionRepository.php

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * Vérifie si un utilisateur est abonné à un provider
     */
    public function isSubscribed(User $subscriber, User $provider): bool
    {
        return $this->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->where('s.subscriber = :subscriber')
                ->andWhere('s.provider = :provider')
                ->andWhere('s.isActive = true')
                ->setParameter('subscriber', $subscriber)
                ->setParameter('provider', $provider)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    /**
     * Récupère tous les abonnements d'un utilisateur
     */
    public function findUserSubscriptions(User $subscriber)
    {
        return $this->createQueryBuilder('s')
            ->where('s.subscriber = :subscriber')
            ->andWhere('s.isActive = true')
            ->setParameter('subscriber', $subscriber)
            ->orderBy('s.subscribedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère tous les abonnés d'un provider
     */
    public function findProviderSubscribers(User $provider)
    {
        return $this->createQueryBuilder('s')
            ->where('s.provider = :provider')
            ->andWhere('s.isActive = true')
            ->setParameter('provider', $provider)
            ->orderBy('s.subscribedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}