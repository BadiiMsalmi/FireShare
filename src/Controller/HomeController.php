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
        $user = $security->getUser();

        $groups = $this->entityManager->getRepository(Groups::class)->findAll();
//        $groups = $this->entityManager->createQueryBuilder()
//            ->select('name')
//            ->from(Groups::class, 'g')
//            ->where('g.isActive = :isActive') // Example condition
//            ->setParameter('isActive', true) // Set the parameter value
//            ->orderBy('g.name', 'ASC') // Optional: Sort the results
//            ->getQuery()
//            ->getResult();


        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'groups' => $groups
        ]);
    }
}
