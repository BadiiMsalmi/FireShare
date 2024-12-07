<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route('/auth', name: 'app_auth')]
    public function renderAuthPage(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        return $this->render('user/auth.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'registrationForm' => $form,
        ]);
    }


    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home'); // Redirect authenticated users
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        return $this->render('user/auth.html.twig', [
            'registrationForm' => $form,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager,AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $roles = $form->get('roles')->getData();
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            if (!is_array($roles)) {
                $roles = [$roles];
            }
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email


            return $this->redirectToRoute('app_login');

        }
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/auth.html.twig', [
            'registrationForm' => $form,
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony handles the logout logic. This method can be blank.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
