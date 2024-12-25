<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Groups;
use App\Entity\Membership;
use App\Form\MembershipType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/membership')]
final class MembershipController extends AbstractController
{
//    #[Route(name: 'app_membership_index', methods: ['GET'])]
//    public function index(EntityManagerInterface $entityManager): Response
//    {
//        $memberships = $entityManager
//            ->getRepository(Membership::class)
//            ->findAll();
//
//        return $this->render('membership/index.html.twig', [
//            'memberships' => $memberships,
//        ]);
//    }

    #[Route('/new', name: 'app_membership_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $membership = new Membership();
        $form = $this->createForm(MembershipType::class, $membership);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($membership);
            $entityManager->flush();

            return $this->redirectToRoute('app_membership_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('membership/new.html.twig', [
            'membership' => $membership,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}', name: 'app_membership_show', methods: ['GET'])]
//    public function show(Membership $membership): Response
//    {
//        return $this->render('membership/show.html.twig', [
//            'membership' => $membership,
//        ]);
//    }

    #[Route('/join/{id}', name: 'join_membership', methods: ['POST'])]
    public function join(int $id, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {

        $user = $security->getUser();

        $groupId = $request->request->get('groupId');
        $group = $entityManager->getRepository(Groups::class)->find($groupId);
        if (!$group) {
            $this->addFlash('error', 'Group not found.');
            return $this->redirectToRoute('homepage');
        }

        $membership = $entityManager->getRepository(Membership::class)->findOneBy([
            'membershipUser' => $user, // Referencing 'membershipUser' as per your entity
            'group' => $group,
        ]);
//        return $this->render('user/index.html.twig');

        if ($membership) {
            $this->addFlash('info', 'You are already a member of this group.');
        }

        $newMembership = new Membership();
        $newMembership->setMembershipUser($user);
        $newMembership->setGroup($group);
        $newMembership->setJoinDate(new \DateTime());

        $entityManager->persist($newMembership);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully joined the group!');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/remove/{id}', name: 'remove_membership', methods: ['POST'])]
    public function delete(int $id, Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
//        if ($this->isCsrfTokenValid('delete'.$membership->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($membership);
//            $entityManager->flush();
//        }

        $user = $security->getUser();

        $groupId = $request->request->get('groupId');
        $group = $entityManager->getRepository(Groups::class)->find($id);
        if (!$group) {
            $this->addFlash('error', 'Group not found.');
            return $this->redirectToRoute('homepage');
        }

        $membership = $entityManager->getRepository(Membership::class)->findOneBy([
            'membershipUser' => $user, // Referencing 'membershipUser' as per your entity
            'group' => $group,
        ]);

        $entityManager->remove($membership);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully removed the membership.');

        return $this->redirectToRoute('app_home');
    }
}
