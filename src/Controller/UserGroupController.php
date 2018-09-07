<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Manager\SlackManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends FOSRestController
{
    /**
     * @Route("/usergroup", name="usergroup_list", methods={"GET"})
     */
    public function getListAction(EntityManagerInterface $em)
    {
        $userGroups = $em->getRepository(UserGroup::class)->findAll();

        $context = new Context();
        $context->addGroup('usergroup_list');
        $context->setSerializeNull(true);

        $view = $this->view($userGroups, 200);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Route("/usergroup/{id}", name="usergroup_details", methods={"GET"})
     */
    public function getDetailsAction(string $id, EntityManagerInterface $em)
    {
        $userGroup = $em->getRepository(UserGroup::class)->findOneBySlackId($id);

        if (null === $userGroup) {
            throw new NotFoundHttpException('This alias was not found');
        }

        $context = new Context();
        $context->addGroup('usergroup_detail');
        $context->setSerializeNull(true);

        $view = $this->view($userGroup, 200);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Route("/usergroup", name="usergroup_post", methods={"POST"})
     */
    public function postUserGroupAction(Request $request, EntityManagerInterface $em, SlackManager $slackManager)
    {
        $rawPost = json_decode($request->getContent(), true);

        $existingUserGroup = $em->getRepository(UserGroup::class)->findOneByHandle($rawPost['handle']);
        if ($existingUserGroup !== null) {
            throw new ConflictHttpException('An alias with this handle already exists');
        }

        if (empty($rawPost['handle']) || empty($rawPost['name']) || empty($rawPost['users'])) {
            throw new BadRequestHttpException(
                'Whoops, couldn\'t process your request. Some parameters are missing'
            );
        }

        $userGroup = (new UserGroup())
            ->setHandle($rawPost['handle'])
            ->setName($rawPost['name'])
            ->setDescription($rawPost['description'])
        ;

        foreach ($rawPost['users'] as $rawUser) {
            $user = $em->getRepository(User::class)
                ->findOneBySlackId($rawUser);

            if ($user === null) {
                throw new NotFoundHttpException(sprintf('User %s was not found', $rawUser));
            }

            $userGroup->addUser($user);
        }

        try {
            $slackResponse = json_decode(
                $slackManager->postSlackUserGroup($userGroup)->getBody()->getContents(),
                true
            );
            echo json_encode($slackResponse);

            $userGroup->setSlackId($slackResponse['usergroup']['id']);
        } catch (ClientException $e) {
            throw $e;
//            throw new HttpException('Whoops, couln\'t post alias to Slack.');
        }

        $em->persist($userGroup);
        $em->flush();

        $context = new Context();
        $context->addGroup('usergroup_detail');
        $context->setSerializeNull(true);

        $view = $this->view($userGroup, 200);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Route("/usergroup/{id}", name="usergroup_delete", methods={"DELETE"})
     */
    public function deleteUserGroupAction(
        string $userGroupSlackId,
        EntityManagerInterface $em,
        SlackManager $slackManager
    ) {
        /** @var UserGroup $userGroup */
        $userGroup = $em->getRepository(UserGroup::class)->findOneBySlackId($userGroupSlackId);

        if (null === $userGroup) {
            throw new NotFoundHttpException('This alias was not found');
        }

        try {
            $slackResponse = json_decode(
                $slackManager->postSlackUserGroup($userGroup)->getBody()->getContents(),
                true
            );
            echo json_encode($slackResponse);
        } catch (ClientException $e) {
            throw $e;
//            throw new HttpException('Whoops, couln\'t delete alias from Slack.');
        }

        $em->remove($userGroup);
        $em->flush();

        return $this->json(sprintf('Alias %s was removed', $userGroup->getName()));
    }
}
