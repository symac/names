<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Result;
use App\Entity\ResultStep;
use App\Repository\ForenameRepository;
use App\Repository\ResultRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class UpdateCountAnagramsCommand extends Command
{
    protected static $defaultName = 'app:update-anagrams-count';

    private $em;
    private $resultRepository;

    public function __construct(string $name = null, EntityManagerInterface $em, ResultRepository $resultRepository)
    {
        $this->em = $em;
        $this->resultRepository = $resultRepository;
        parent::__construct($name);

    }

    protected function configure()
    {
        $this
            ->setDescription("Update the column anagram count");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Updating results");

        $results = $this->resultRepository->findAll();
        foreach ($results as $result) {
            print $result->getSlug()."\n";
            print $result->getCountAnagrams()."\n";
            $result->setCountAnagrams($result->computeCountAnagrams());
            print $result->getCountAnagrams()."\n";
            print "\n\n";
            $this->em->persist($result);
        }
        $this->em->flush();

        return 1;
    }
}
