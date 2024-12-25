<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/posts')]
class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

//    #[Route('', name: 'post_index', methods: ['GET'])]
//    public function index(): Response
//    {
//        $posts = $this->entityManager->getRepository(Post::class)->findAll();
//
//        return $this->render('post/index.html.twig', [
////            'posts' => $posts,
//        ]);
//    }

    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $title = $request->request->get('title');
        $group = $request->request->get('group');
        $content = $request->request->get('content');

        // Check if title is null or empty
        if ($title === null) {
            // Handle the case where title is not provided
            throw new \Exception('Title cannot be empty');
        }

        // Process and save the data (example)
        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);

        $user = $security->getUser();
        $post->setAuthor($user);

        $groupEntity = $entityManager->getRepository(Groups::class)->find($group);
        $post->setGroup($groupEntity);

        $this->entityManager->persist($post);
        $this->entityManager->flush();


//        $form = $this->createForm(PostType::class, $post);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->entityManager->persist($post);
//            $this->entityManager->flush();
//        }

        return $this->redirectToRoute('app_home'); // Redirect or return a response
    }

//    #[Route('/{id}', name: 'post_show', methods: ['GET'])]
//    public function show(Post $post): Response
//    {
//        return $this->render('post/show.html.twig', [
//            'post' => $post,
//        ]);
//    }
//
//    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Post $post): Response
//    {
//        $form = $this->createForm(PostType::class, $post);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->entityManager->flush();
//
//            return $this->redirectToRoute('post_index');
//        }
//
//        return $this->render('post/edit.html.twig', [
//            'post' => $post,
//            'form' => $form->createView(),
//        ]);
//    }
//
//    #[Route('/{id}', name: 'post_delete', methods: ['DELETE'])]
//    public function delete(Request $request, Post $post): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
//            $this->entityManager->remove($post);
//            $this->entityManager->flush();
//        }
//
//        return $this->redirectToRoute('post_index');
//    }
}
