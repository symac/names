<?php

namespace App\Command;

use App\Entity\Forename;
use App\Repository\ForenameRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class CleanDatabaseCommand extends Command
{
    protected static $defaultName = 'app:clean-db';

    private $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription("Remove from DB anything that won't get used")
        ;
    }

    private function runSql($sql) {
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return  $stmt->rowCount();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Cleaning database");

        $io->writeln("Deleting non latin surnames");
        $count = $this->runSql("DELETE FROM `surname` where convert(label using latin1) != label");
        $io->writeln(" > $count deletions\n");

        $io->writeln("Deleting non latin forenames");
        $count = $this->runSql('DELETE FROM `forename` where convert(label using "latin1") != label;');
        $io->writeln(" > $count deletions\n");

        $io->writeln("Deleting very small forenames");
        $count = $this->runSql('DELETE FROM `forename` where LENGTH(label) <= 2;');
        $io->writeln(" > $count deletions\n");

        $io->writeln("Deleting very small surnames");
        $count = $this->runSql('DELETE FROM `surname` where LENGTH(label) <= 2;');
        $io->writeln(" > $count deletions\n");

        $io->writeln("Deleting surnames starting with a dash");
        $count = $this->runSql('DELETE FROM `surname` where label like \'"%\'');
        $io->writeln(" > $count deletions\n");


//;
//;

        exit;

    }
}
