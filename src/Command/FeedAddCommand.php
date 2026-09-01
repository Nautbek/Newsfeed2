<?php

namespace App\Command;

use App\Entity\Feed;
use App\Validator\ReachableFeed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'FeedAddCommand',
    description: 'Add a short description for your command',
)]
class FeedAddCommand extends Command
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::OPTIONAL, 'Url');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('arg1');

        $violations = $this->validator->validate($url, new ReachableFeed());

        if (count($violations) > 0) {
            $io->error(array_map(fn (ConstraintViolation $violation): string => $violation->getMessage(), $violations));

            return Command::FAILURE;
        }

        $feed = new Feed();
        $feed->setUrl($url);
        $this->entityManager->persist($feed);
        $this->entityManager->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
