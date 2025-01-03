<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Membership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $groups = $entityManager->getRepository(Groups::class)
            ->findBy(['Moderator' => $user->getId()]);

        $selectedGroup = null;
        $memberCount = 0;
        $postCount = 0;
        $createdAt = null;
        $lastMembers = [];
        $recentPosts = [];
        $notifications = [];
        $activeMembers = [];
        $growthData = [];
        $membershipStats = [];

        if (!empty($groups)) {
            $selectedGroup = $groups[0];
            $memberCount = $selectedGroup->getMemberships()->count();
            $postCount = $selectedGroup->getPostes()->count();
            $createdAt = $selectedGroup->getCreatedAt();

            $lastMembers = $entityManager->getRepository(Membership::class)
                ->createQueryBuilder('m')
                ->where('m.group = :group')
                ->setParameter('group', $selectedGroup)
                ->orderBy('m.joinDate', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            $recentPosts = $this->getRecentPosts($selectedGroup, $entityManager);
            $growthData = $this->getMemberGrowthData($selectedGroup, $entityManager);
            $totalMembers = $selectedGroup->getMemberships()->count();
            $newMembersThisMonth = $this->getNewMembersThisMonth($selectedGroup, $entityManager);
            $membershipStats = [
                'totalMembers' => $totalMembers,
                'newMembersThisMonth' => $newMembersThisMonth,
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'memberCount' => $memberCount,
            'postCount' => $postCount,
            'createdAt' => $createdAt,
            'lastMembers' => $lastMembers,
            'recentPosts' => $recentPosts,
            'notifications' => $notifications,
            'activeMembers' => $activeMembers,
            'growthData' => $growthData,
            'membershipStats' => $membershipStats,
        ]);
    }

    #[Route('/dashboard/group/{id}', name: 'dashboard_group_details', methods: ['GET'])]
    public function groupDetails(int $id, EntityManagerInterface $entityManager): Response
    {
        $group = $entityManager->getRepository(Groups::class)->find($id);

        if (!$group) {
            throw $this->createNotFoundException('Group not found.');
        }

        $memberCount = $group->getMemberships()->count();
        $postCount = $group->getPostes()->count();
        $createdAt = $group->getCreatedAt();

        $lastMembers = $entityManager->getRepository(Membership::class)
            ->createQueryBuilder('m')
            ->where('m.group = :group')
            ->setParameter('group', $group)
            ->orderBy('m.joinDate', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $recentPosts = $this->getRecentPosts($group, $entityManager);
        $growthData = $this->getMemberGrowthData($group, $entityManager);
        $totalMembers = $group->getMemberships()->count();
        $newMembersThisMonth = $this->getNewMembersThisMonth($group, $entityManager);
        $membershipStats = [
            'totalMembers' => $totalMembers,
            'newMembersThisMonth' => $newMembersThisMonth,
        ];

        return $this->render('dashboard/index.html.twig', [
            'groups' => $entityManager->getRepository(Groups::class)
                ->findBy(['Moderator' => $this->getUser()->getId()]),
            'selectedGroup' => $group,
            'memberCount' => $memberCount,
            'postCount' => $postCount,
            'createdAt' => $createdAt,
            'lastMembers' => $lastMembers,
            'recentPosts' => $recentPosts,
            'growthData' => $growthData,
            'membershipStats' => $membershipStats,
        ]);
    }

    private function getRecentPosts(Groups $group, EntityManagerInterface $entityManager): array
    {
        return $entityManager->getRepository('App\Entity\Post')
            ->createQueryBuilder('p')
            ->where('p.group = :group')
            ->setParameter('group', $group)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    private function getMemberGrowthData(Groups $group, EntityManagerInterface $entityManager): array
    {
        $connection = $entityManager->getConnection();
        $sql = 'SELECT DATE(m.join_date) AS joinDate, COUNT(m.id) AS memberCount 
                FROM membership m 
                WHERE m.group_id = :groupId 
                GROUP BY DATE(m.join_date) 
                ORDER BY joinDate ASC';

        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery(['groupId' => $group->getId()]);

        return $resultSet->fetchAllAssociative();
    }

    private function getNewMembersThisMonth(Groups $group, EntityManagerInterface $entityManager): int
    {
        $connection = $entityManager->getConnection();
        $sql = 'SELECT COUNT(m.id) AS newMembers 
                FROM membership m 
                WHERE m.group_id = :groupId 
                AND MONTH(m.join_date) = MONTH(CURRENT_DATE()) 
                AND YEAR(m.join_date) = YEAR(CURRENT_DATE())';

        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery(['groupId' => $group->getId()]);

        $result = $resultSet->fetchAssociative();
        return $result['newMembers'] ?? 0;
    }
}
