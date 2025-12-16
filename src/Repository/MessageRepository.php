<?php

// src/Repository/MessageRepository.php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Récupère la conversation entre deux utilisateurs
     */
    public function findConversation(User $user1, User $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2)')
            ->orWhere('(m.sender = :user2 AND m.receiver = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.sentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la liste des conversations d'un utilisateur avec le dernier message
     */
    public function findUserConversations(User $user)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb
            ->select('IDENTITY(m.sender) as senderId, IDENTITY(m.receiver) as receiverId, MAX(m.sentAt) as lastMessageDate')
            ->where('m.sender = :user')
            ->orWhere('m.receiver = :user')
            ->setParameter('user', $user)
            ->groupBy('senderId, receiverId')
            ->orderBy('lastMessageDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les messages non lus pour un utilisateur
     */
    public function countUnreadMessages(User $receiver): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.receiver = :receiver')
            ->andWhere('m.isRead = false')
            ->setParameter('receiver', $receiver)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Compte les messages non lus dans une conversation
     */
    public function countUnreadInConversation(User $receiver, User $sender): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.receiver = :receiver')
            ->andWhere('m.sender = :sender')
            ->andWhere('m.isRead = false')
            ->setParameter('receiver', $receiver)
            ->setParameter('sender', $sender)
            ->getQuery()
            ->getSingleScalarResult();
    }
}