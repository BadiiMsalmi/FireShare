<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Membership;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/updateprofile', name: 'update_profile')]
    public function update(Security $security): Response {

        return $this->render('user/update.html.twig', []);
    }
}
