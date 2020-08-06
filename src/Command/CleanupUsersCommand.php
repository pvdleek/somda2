<?php

namespace App\Command;

use App\Entity\ForumPost;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupUsersCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:clean-up-users';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(self::$defaultName);

        $this->doctrine = $doctrine;
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Clean users that were not activated within 5 days');
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var User[] $users
         */
        $users = $this->doctrine->getRepository(User::class)->findNonActivated();
        foreach ($users as $user) {
            $numberOfPosts = $this->doctrine->getRepository(ForumPost::class)->findBy(['author' => $user]);
            if (count($user->getSpots()) < 1 && count($numberOfPosts) < 1) {
                $this->doctrine->getManager()->remove($user->info);
                $this->doctrine->getManager()->remove($user);
                $this->doctrine->getManager()->flush();
            }
        }

        return 0;
    }
}
