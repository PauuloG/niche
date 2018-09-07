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

class UsersListCommand extends Command
{
    protected static $defaultName = 'users:list';

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
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Lists users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->em->getRepository(User::class)->findAll();

        $usersListHeaders = [
            'Id',
            'Slack id',
            'Slack name',
            'Slack real name'
        ];
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

        $io->text(sprintf(
            'There are <info>%s</info> items in this list',
            count($usersList)
        ));
        $io->table($usersListHeaders, $usersList);
    }
}
