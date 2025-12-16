<?php
namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();

            if ($user->getRole() === 'SERVICE_OWNER' && !$user->isValidated()) {
                $this->addFlash('error', 'Votre compte Owner est en attente de validation par l’administrateur.');
                return $this->redirectToRoute('app_logout');
            }

            if (!$user->isActive()) {
                $this->addFlash('error', 'Votre compte est désactivé.');
                return $this->redirectToRoute('app_logout');
            }

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('admin_dashboard');
            } else {
                return $this->redirectToRoute('client_home');
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {
        throw new \Exception('This should never be reached!');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {

            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $role = $request->request->get('role');
            $cin = $request->request->get('cin');

            if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Email déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setName($name)->setEmail($email)->setRole($role)->setCreatedAt(new \DateTime());

            if ($role === 'SERVICE_OWNER') {
                $user->setCin($cin);
                $user->setIsValidated(false);

                $this->createAdminNotification($em, $user);
                $this->createWelcomeNotification($em, $user, $role);

            } else {
                $user->setIsValidated(true);
                $this->createWelcomeNotification($em, $user,$role);

            }

            $photoFile = $request->files->get('photo');
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($this->getParameter('kernel.project_dir').'/public/uploads', $newFilename);
                $user->setPhoto($newFilename);
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            if ($role === 'SERVICE_OWNER') {
                $this->addFlash('success', 'Inscription réussie. Vous recevrez un email après la confirmation de votre compte par l’admin.');
            } else {
                $this->addFlash('success', 'Inscription réussie. Vous pouvez vous connecter.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig');
    }

// Méthode pour créer une notification pour l'admin
    private function createAdminNotification(EntityManagerInterface $em, User $newOwner): void
    {
        // Trouver l'admin (vous pouvez adapter selon votre logique)
        $admin = $em->getRepository(User::class)->findOneBy(['role' => 'ADMIN']);

        // Si pas d'admin, on prend le premier utilisateur avec rôle ADMIN
        if (!$admin) {
            $admin = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);
        }

        if ($admin) {
            $notification = new Notification();
            $notification->setTitle('Nouveau propriétaire à valider');
            $notification->setMessage("Un nouveau propriétaire s'est inscrit : {$newOwner->getName()} ({$newOwner->getEmail()}). Veuillez vérifier son CIN : {$newOwner->getCin()}");
            $notification->setType('warning');
            $notification->setUser($admin);
            $notification->setIsRead(false);

            $em->persist($notification);
        }
    }

    private function createWelcomeNotification(EntityManagerInterface $em, User $newUser, string $role): void
    {
        $notification = new Notification();

        // Messages différents selon le rôle
        if ($role === 'SERVICE_OWNER') {
            $notification->setTitle('Bienvenue sur App-Review !');
            $notification->setMessage("Bonjour {$newUser->getName()}, bienvenue sur App-Review ! Votre compte propriétaire est en attente de validation par notre équipe. Vous recevrez une notification une fois votre compte validé. En attendant, vous pouvez explorer la plateforme.");
            $notification->setType('info');
        } else {
            $notification->setTitle('Bienvenue sur App-Review !');
            $notification->setMessage("Bonjour {$newUser->getName()}, bienvenue sur App-Review ! Nous sommes ravis de vous compter parmi nous. Commencez dès maintenant à explorer les commerces autour de vous, laisser vos avis et découvrir de nouveaux endroits.");
            $notification->setType('success');
        }

        $notification->setUser($newUser);
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTime());

        $em->persist($notification);

        // Pour les utilisateurs clients, créer une deuxième notification avec des conseils
        if ($role !== 'SERVICE_OWNER') {
            $tipNotification = new Notification();
            $tipNotification->setTitle('Conseils pour bien débuter');
            $tipNotification->setMessage("💡 Astuce : Complétez votre profil pour recevoir des recommandations personnalisées. N'hésitez pas à partager vos expériences en laissant des avis détaillés avec photos !");
            $tipNotification->setType('info');
            $tipNotification->setUser($newUser);
            $tipNotification->setIsRead(false);
            $tipNotification->setCreatedAt(new \DateTime());

            $em->persist($tipNotification);
        }
    }
}
