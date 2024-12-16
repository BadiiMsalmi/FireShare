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

        $posts = $this->entityManager->getRepository(Post::class)->findAll();
        $currentUser = $security->getUser();

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


        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'groups' => $resultGroups,
            'memberships' => $resultMemberships,

        ]);
    }
}
