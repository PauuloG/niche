<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Manager\SlackManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserGroupsUpdateCommand extends Command
{
    protected static $defaultName = 'groups:update';

    /**
     * @var SlackManager
     */
    private $slackManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UsersUpdateCommand constructor.
     * @param SlackManager $slackManager
     * @param EntityManagerInterface $em
     */
    public function __construct(SlackManager $slackManager, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->slackManager = $slackManager;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates the list of users using Slack API')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $response = json_decode($this->slackManager->getSlackUserGroupsList()->getBody()->getContents(), true);
        $rawGroups = $response['usergroups'];

        foreach ($rawGroups as $rawGroup) {
            $groupSlackId = $rawGroup['id'];
            $groupName = $rawGroup['name'];
            $groupHandle = $rawGroup['handle'];

            $groupDetails = json_decode(
                $this->slackManager->getSlackUserGroup($rawGroup['id'])->getBody()->getContents(),
                true
            );

            $userGroup = $this->em->getRepository(UserGroup::class)
                ->findGroupOrCreate($groupSlackId);

            $userGroup
                ->setSlackId($groupSlackId)
                ->setName($groupName)
                ->setHandle($groupHandle)
            ;

            $usersList = [];
            foreach ($groupDetails['users'] as $rawUser) {
                /** @var User $user */
                $user = $this->em->getRepository(User::class)->findOneBySlackId($rawUser);
                $usersList[] = $user->getSlackName();
                if ($user !== null) {
                    $userGroup->addUser($user);
                    $user->addUserGroup($userGroup);
                }
                $this->em->persist($user);
            }

            $io->text(sprintf(
                '<info>* Synced group %s (@%s %s) with users : </info>%s',
                $userGroup->getName(),
                $userGroup->getHandle(),
                $userGroup->getSlackId(),
                implode(', ', $usersList)
            ));

            $this->em->persist($userGroup);
        }
        $this->em->flush();
    }
}
