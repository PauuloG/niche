<?php

namespace App\Command;

use App\Entity\User;
use App\Manager\SlackManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UsersUpdateCommand extends Command
{
    protected static $defaultName = 'users:update';

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

        $response = json_decode($this->slackManager->getSlackUsersList()->getBody()->getContents(), true);
        $rawUsers = $response['members'];
        $results = [
            'added' => [],
            'known' => []
        ];

        foreach ($rawUsers as $rawUser) {
            if ($rawUser['is_bot'] ||
                $rawUser['deleted'] ||
                $rawUser['is_ultra_restricted'] ||
                $rawUser['is_restricted'] ||
                $rawUser['name'] == 'slackbot'
            ) {
                continue;
            } else {
                $slackName =
                    $rawUser['profile']['display_name_normalized'] == ''
                        ? $rawUser['name']
                        : $rawUser['profile']['display_name_normalized'];

                $users[] = $rawUser['name'];
                $existingUser = $this->em->getRepository(User::class)
                    ->findUserBySlackId($rawUser['id']);

                if (null !== $existingUser) {
                    $user = $existingUser;
                    $results['known'][] = $slackName;
                } else {
                    $user = (new User());
                    $results['added'][] = $slackName;
                }

                $user
                    ->setSlackId($rawUser['id'])
                    ->setSlackName($slackName)
                    ->setSlackRealName($rawUser['profile']['real_name_normalized'])
                    ->setEmail($rawUser['profile']['email'])
                    ->setImageUrl($rawUser['profile']['image_192'])
                ;

                $this->em->persist($user);
            }
        }
        $this->em->flush();

        $io->text(sprintf(
            'There are %s users in this list, (%s users added)',
            count($results['known']) + count($results['added']),
            count($results['added'])
        ));

        $added = array_map(function ($name) {
            return sprintf('<info>NEW! %s</info>', $name);
        }, $results['added']);

        $display = array_merge($added, $results['known']);

        $io->listing($display);
    }
}
