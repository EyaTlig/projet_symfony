<?php
// src/Controller/NotificationController.php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationController extends AbstractController
{


    #[Route('/api/notifications', name: 'notifications_list')]
    public function listNotifications(EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(
                ['user' => $this->getUser()],
                ['createdAt' => 'DESC']
            );

        $formattedNotifications = [];
        foreach ($notifications as $notification) {
            $formattedNotifications[] = [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
                'type' => $notification->getType(),
                'isRead' => $notification->isRead(),
                'createdAt' => $notification->getCreatedAt()->format('d/m/Y H:i'),
            ];
        }

        return new JsonResponse($formattedNotifications);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour le compteur)
    #[Route('/api/notifications/unread-count', name: 'notifications_unread_count')]
    public function unreadCount(EntityManagerInterface $em): JsonResponse
    {
        $count = $em->getRepository(Notification::class)
            ->count(['user' => $this->getUser(), 'isRead' => false]);

        return new JsonResponse(['count' => $count]);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour marquer une notification comme lue)
    #[Route('/notification/{id}/read', name: 'notification_mark_read', methods: ['POST'])]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $notification->setIsRead(true);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour marquer comme non lu)
    #[Route('/notification/{id}/unread', name: 'notification_mark_unread', methods: ['POST'])]
    public function markAsUnread(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $notification->setIsRead(false);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour supprimer)
    #[Route('/notification/{id}/delete', name: 'notification_delete', methods: ['DELETE'])]
    public function delete(Notification $notification, EntityManagerInterface $em): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $em->remove($notification);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour voir les détails)
    #[Route('/notification/{id}/details', name: 'notification_details')]
    public function details(Notification $notification): JsonResponse
    {
        if ($notification->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        return new JsonResponse([
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'type' => $notification->getType(),
            'createdAt' => $notification->getCreatedAt()->format('d/m/Y H:i'),
        ]);
    }

    #[Route('/notification/{id}', name: 'notification_show')]
    public function show(Notification $notification, EntityManagerInterface $em): Response
    {
        // Marquer comme lu
        if (!$notification->isRead()) {
            $notification->setIsRead(true);
            $em->flush();
        }

        // Rediriger vers l'action appropriée selon le type
        if (str_contains($notification->getTitle(), 'propriétaire')) {
            return $this->redirectToRoute('user_index');
        }

        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    // ✅ AJOUTEZ CETTE ROUTE (pour tout marquer comme lu)
    #[Route('/notifications/mark-all-read', name: 'notifications_mark_all_read', methods: ['POST'])]
    public function markAllAsRead(EntityManagerInterface $em): JsonResponse
    {
        $notifications = $em->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser(), 'isRead' => false]);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/notifications/count', name: 'notification_count')]
    public function count(EntityManagerInterface $em): JsonResponse
    {
        $count = $em->getRepository(Notification::class)
            ->count(['user' => $this->getUser(), 'isRead' => false]);

        return new JsonResponse(['count' => $count]);
    }
}