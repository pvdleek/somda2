<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\ForumPost;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:clean-up-users',
    description: 'Clean users that were not activated within 5 days',
    hidden: false,
)]

class CleanupUsersCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        /**
         * @var User[] $users
         */
        $users = $this->doctrine->getRepository(User::class)->findNonActivated();
        foreach ($users as $user) {
            $numberOfPosts = $this->doctrine->getRepository(ForumPost::class)->findBy(['author' => $user]);
            if (\count($user->getSpots()) < 1 && \count($numberOfPosts) < 1) {
                foreach ($user->getPreferences() as $preference) {
                    $this->doctrine->getManager()->remove($preference);
                }
                $this->doctrine->getManager()->remove($user->info);
                $this->doctrine->getManager()->remove($user);
                $this->doctrine->getManager()->flush();
            }
        }

        return 0;
    }
}
