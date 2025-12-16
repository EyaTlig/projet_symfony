<?php
// src/Controller/SubscriptionController.php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/subscription')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class SubscriptionController extends AbstractController
{
    #[Route('/toggle/{id}', name: 'subscription_toggle', methods: ['POST'])]
    public function toggle(
        User $provider,
        SubscriptionRepository $subscriptionRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $subscriber = $this->getUser();

        // Vérifier que l'utilisateur ne s'abonne pas à lui-même
        if ($subscriber === $provider) {
            return new JsonResponse(['error' => 'Cannot subscribe to yourself'], 400);
        }

        // Vérifier que le provider est bien un SERVICE_OWNER
        if ($provider->getRole() !== 'SERVICE_OWNER') {
            return new JsonResponse(['error' => 'Can only subscribe to service owners'], 400);
        }

        // Vérifier si l'abonnement existe déjà
        $existing = $subscriptionRepo->findOneBy([
            'subscriber' => $subscriber,
            'provider' => $provider
        ]);

        if ($existing) {
            // Toggle l'état actif
            $existing->setIsActive(!$existing->isActive());
            $em->flush();

            return new JsonResponse([
                'status' => $existing->isActive() ? 'subscribed' : 'unsubscribed'
            ]);
        }

        // Créer un nouvel abonnement
        $subscription = new Subscription();
        $subscription->setSubscriber($subscriber);
        $subscription->setProvider($provider);
        $subscription->setIsActive(true);

        $em->persist($subscription);
        $em->flush();

        return new JsonResponse(['status' => 'subscribed']);
    }

    #[Route('/my-subscriptions', name: 'my_subscriptions')]
    public function mySubscriptions(SubscriptionRepository $subscriptionRepo): Response
    {
        $subscriptions = $subscriptionRepo->findUserSubscriptions($this->getUser());

        return $this->render('subscription/my_subscriptions.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/my-subscribers', name: 'my_subscribers')]
    #[IsGranted('ROLE_SERVICE_OWNER')]
    public function mySubscribers(SubscriptionRepository $subscriptionRepo): Response
    {
        $subscribers = $subscriptionRepo->findProviderSubscribers($this->getUser());

        return $this->render('subscription/my_subscribers.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    #[Route('/status/{id}', name: 'subscription_status')]
    public function status(User $provider, SubscriptionRepository $subscriptionRepo): JsonResponse
    {
        $isSubscribed = $subscriptionRepo->isSubscribed($this->getUser(), $provider);

        return new JsonResponse(['isSubscribed' => $isSubscribed]);
    }
}