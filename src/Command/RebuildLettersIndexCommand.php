<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use App\Service\PseudonameFinder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rebuild-letters-index',
    description: 'Add a short description for your command',
)]
class RebuildLettersIndexCommand extends Command
{
    private $em;
    private $pseudonameFinder;
    public function __construct(EntityManagerInterface $em, PseudonameFinder $pseudonameFinder, string $name = null)
    {
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);


        $this->pseudonameFinder = $pseudonameFinder;
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    private function buildIndexForClass(string $class)
    {
        ini_set('memory_limit', '-1');
        $entities = $this->em->getRepository($class)->findAll();
        $count = 0;
        foreach ($entities as $entity) {
            $lettersIndex = $this->pseudonameFinder->constructLettersIndex($entity->getLabels());
            $entity->setLettersIndex($lettersIndex);
            $this->em->persist($entity);
            $count++;
            if ($count % 100000 == 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $this->buildIndexForClass(Forename::class);
        $this->buildIndexForClass(Surname::class);
        return Command::SUCCESS;
    }
}
