<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/test', name: 'test')]
    public function test(): Response {
        return $this->render('user/index.html.twig');
    }

    #[Route('/home', name: 'app_home')]
    public function index(Security $security): Response
    {

        $currentUser = $security->getUser();

//        $queryPosts = $this->entityManager->getRepository(Post::class)->findAll();
        $queryPosts = $this->entityManager->createQuery(
            "SELECT count(p.id) nbr, p.id As post_id, p.title, p.content, p.upvotes, p.downvotes, p.createdAt, g.name, u.firstName, u.lastName
                FROM App\Entity\Post p, App\Entity\Groups g, App\Entity\Membership m, App\Entity\User u
                WHERE p.group = g.id AND g.id = m.group AND m.membershipUser = :currentUser
                Group by p.id"
        );
        $queryPosts->setParameter('currentUser', $currentUser->getId());
        $queryPosts = $queryPosts->getResult();

//        $groups = $this->entityManager->getRepository(Groups::class)->findAll();
        $queryGroups = $this->entityManager->createQuery(
            "SELECT g.id, g.name FROM App\Entity\Groups g WHERE g.id NOT IN 
                (SELECT IDENTITY(m.group) FROM App\Entity\Membership m WHERE m.membershipUser = :userId)"
        );
        $queryGroups->setParameter('userId', $currentUser->getId());
        $resultGroups = $queryGroups->getResult();

        $queryMemberships = $this->entityManager->createQuery(
            "SELECT g.id, g.name FROM App\Entity\Groups g WHERE g.id IN 
                (SELECT IDENTITY(m.group) FROM App\Entity\Membership m WHERE m.membershipUser = :userId)"
        );
        $queryMemberships->setParameter('userId', $currentUser->getId());
        $resultMemberships = $queryMemberships->getResult();

//        if ($queryPosts) {
//            $queryPosts = null;
//        }

        return $this->render('home/index.html.twig', [
            "user" => $currentUser,
            'posts' => $queryPosts,
            'groups' => $resultGroups,
            'memberships' => $resultMemberships,

        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(Security $security): Response {

        $currentUser = $security->getUser();

        $name = $currentUser->getFirstName() . ' ' . $currentUser->getLastName();

        return $this->render('user/profile.html.twig', [
            'user' => $name,
        ]);
    }

    #[Route('/updateprofile', name: 'update_profile')]
    public function update(Security $security): Response {

        $currentUser = $security->getUser();

        $name = $currentUser->getFirstName() . ' ' . $currentUser->getLastName();

        return $this->render('user/update.html.twig', [
            "currentUser" => $currentUser,
            'user' => $name,
        ]);
    }

    #[Route('/editprofile', name: 'update_profile_post', methods: ['POST'])]
    public function updateprofile(Security $security, Request $request, EntityManagerInterface $entityManager): Response {

        $user = $security->getUser();

        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Update only if the field is provided and has changed
        if (!empty($firstName)) {
            $user->setFirstName($firstName);
        }

        if (!empty($lastName)) {
            $user->setLastName($lastName);
        }

        if (!empty($email)) {
            $user->setEmail($email);
        }

        if (empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

//            if (!password_verify($password, $user->getPassword())) {
//                $user->setPassword($hashedPassword);
//            }
        }

        $entityManager->flush();


        return $this->redirectToRoute('app_home');
    }

}
