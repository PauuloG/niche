<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\SlackManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(EntityManagerInterface $em, SlackManager $slackManager)
    {
        $users = $em->getRepository(User::class)->findAll();

        $usersList = [];
        /** @var User $user */
        foreach ($users as $user) {
            $usersList[] = [
                $user->getId(),
                $user->getSlackId(),
                $user->getSlackName(),
                $user->getSlackRealName()
            ];
        }

        return $this->render('default/index.html.twig', [
            'data' => $usersList,
            'controller_name' => 'DefaultController',
        ]);
    }
}
