<?php
// src/Controller/MessageController.php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/messages')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MessageController extends AbstractController
{
    #[Route('/messages', name: 'messages_index')]
    public function index(
        MessageRepository $messageRepo,
        SubscriptionRepository $subscriptionRepo,
        EntityManagerInterface $em
    ): Response {
        $currentUser = $this->getUser();
        $userRole = $currentUser->getRole();

        // Déterminer les contacts disponibles selon le rôle
        $availableContacts = [];

        if ($userRole === 'ADMIN') {
            // L'admin peut contacter tout le monde
            $availableContacts = $em->getRepository(User::class)->findAll();
        } elseif ($userRole === 'SERVICE_OWNER') {
            // Le provider peut contacter ses abonnés et l'admin
            $subscribers = $subscriptionRepo->findProviderSubscribers($currentUser);
            $availableContacts = array_map(fn($sub) => $sub->getSubscriber(), $subscribers);

            // Ajouter l'admin
            $admin = $em->getRepository(User::class)->findOneBy(['role' => 'ADMIN']);
            if ($admin) {
                $availableContacts[] = $admin;
            }
        } else {
            // CUSTOMER : peut contacter les providers auxquels il est abonné
            $subscriptions = $subscriptionRepo->findUserSubscriptions($currentUser);
            $availableContacts = array_map(fn($sub) => $sub->getProvider(), $subscriptions);
        }

        // Récupérer les conversations existantes
        $conversations = $messageRepo->findUserConversations($currentUser);

        // Enrichir avec les détails des utilisateurs
        $enrichedConversations = [];
        foreach ($conversations as $conv) {
            $otherUserId = ($conv['senderId'] == $currentUser->getId())
                ? $conv['receiverId']
                : $conv['senderId'];

            $otherUser = $em->getRepository(User::class)->find($otherUserId);
            if ($otherUser) {
                $unreadCount = $messageRepo->countUnreadInConversation($currentUser, $otherUser);
                $enrichedConversations[] = [
                    'user' => $otherUser,
                    'lastMessageDate' => $conv['lastMessageDate'],
                    'unreadCount' => $unreadCount
                ];
            }
        }

        return $this->render('messages/index.html.twig', [
            'conversations' => $enrichedConversations,
            'availableContacts' => $availableContacts,
            'unreadTotal' => $messageRepo->countUnreadMessages($currentUser)
        ]);
    }

    #[Route('/conversation/{id}', name: 'messages_conversation')]
    public function conversation(
        User $otherUser,
        MessageRepository $messageRepo,
        SubscriptionRepository $subscriptionRepo,
        EntityManagerInterface $em
    ): Response {
        $currentUser = $this->getUser();

        // Vérifier si la conversation est autorisée
        if (!$this->canCommunicate($currentUser, $otherUser, $subscriptionRepo)) {
            $this->addFlash('error', 'Vous ne pouvez pas communiquer avec cet utilisateur.');
            return $this->redirectToRoute('messages_index');
        }

        // Récupérer les messages
        $messages = $messageRepo->findConversation($currentUser, $otherUser);

        // Marquer les messages reçus comme lus
        foreach ($messages as $message) {
            if ($message->getReceiver() === $currentUser && !$message->isRead()) {
                $message->setIsRead(true);
            }
        }
        $em->flush();

        return $this->render('messages/conversation.html.twig', [
            'otherUser' => $otherUser,
            'messages' => $messages
        ]);
    }

    #[Route('/send/{id}', name: 'messages_send', methods: ['POST'])]
    public function send(
        User $receiver,
        Request $request,
        SubscriptionRepository $subscriptionRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $sender = $this->getUser();

        // Vérifier si la communication est autorisée
        if (!$this->canCommunicate($sender, $receiver, $subscriptionRepo)) {
            return new JsonResponse(['error' => 'Communication not allowed'], 403);
        }

        $content = trim($request->request->get('content', ''));

        if (empty($content)) {
            return new JsonResponse(['error' => 'Message cannot be empty'], 400);
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent($content);

        $em->persist($message);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sentAt' => $message->getSentAt()->format('Y-m-d H:i:s'),
                'sender' => [
                    'id' => $sender->getId(),
                    'name' => $sender->getName()
                ]
            ]
        ]);
    }

    #[Route('/mark-read/{id}', name: 'messages_mark_read', methods: ['POST'])]
    public function markAsRead(Message $message, EntityManagerInterface $em): JsonResponse
    {
        if ($message->getReceiver() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $message->setIsRead(true);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/unread-count', name: 'messages_unread_count')]
    public function unreadCount(MessageRepository $messageRepo): JsonResponse
    {
        $count = $messageRepo->countUnreadMessages($this->getUser());
        return new JsonResponse(['count' => $count]);
    }

    /**
     * Vérifie si deux utilisateurs peuvent communiquer
     */
    private function canCommunicate(
        User $user1,
        User $user2,
        SubscriptionRepository $subscriptionRepo
    ): bool {
        // L'admin peut communiquer avec tout le monde
        if ($user1->getRole() === 'ADMIN' || $user2->getRole() === 'ADMIN') {
            return true;
        }

        // Un provider peut communiquer avec ses abonnés
        if ($user1->getRole() === 'SERVICE_OWNER') {
            return $subscriptionRepo->isSubscribed($user2, $user1);
        }

        if ($user2->getRole() === 'SERVICE_OWNER') {
            return $subscriptionRepo->isSubscribed($user1, $user2);
        }

        // Les customers ne peuvent pas communiquer entre eux
        return false;
    }
}