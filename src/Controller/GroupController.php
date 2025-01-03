<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Membership;
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/group')]
final class GroupController extends AbstractController
{
//    #[Route(name: 'app_group_index', methods: ['GET'])]
//    public function index(EntityManagerInterface $entityManager): Response
//    {
//        $groups = $entityManager
//            ->getRepository(Groups::class)
//            ->findAll();
//
//        return $this->render('group/index.html.twig', [
//            'groups' => $groups,
//        ]);
//    }

    #[Route('/newgroup', name: 'add_new_group', methods: ['POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');

        $currentUser = $security->getUser();

        $group = new Groups();
        if (empty($name)) {
            return $this->redirectToRoute('app_home');
        }
        $group->setName($name);

        $group->setDescription($description);
        $group->setModerator($currentUser);
        $group->setCreatedAt(new \DateTime('now')); // Set the default value

        $entityManager->persist($group);
        $entityManager->flush();

        $group = $entityManager->getRepository(Groups::class)->find($group->getId());
        if (!$group) {
            $this->addFlash('error', 'Group not found.');
            return $this->redirectToRoute('app_home');
        }

        $membership = $entityManager->getRepository(Membership::class)->findOneBy([
            'membershipUser' => $currentUser, // Referencing 'membershipUser' as per your entity
            'group' => $group,
        ]);
//        return $this->render('user/index.html.twig');

        if ($membership) {
            $this->addFlash('info', 'You are already a member of this group.');
            return $this->redirectToRoute('app_home');
        }

        $newMembership = new Membership();
        $newMembership->setMembershipUser($currentUser);
        $newMembership->setGroup($group);
        $newMembership->setJoinDate(new \DateTime());

        $entityManager->persist($newMembership);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');

    }

//    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
//    public function show(Groups $group): Response
//    {
//        return $this->render('group/show.html.twig', [
//            'group' => $group,
//        ]);
//    }
//
//    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Groups $group, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(GroupType::class, $group);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('group/edit.html.twig', [
//            'group' => $group,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
//    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($group);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
//    }
}
