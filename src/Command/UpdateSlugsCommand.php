<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use App\Repository\ForenameRepository;
use App\Repository\SurnameRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateSlugsCommand extends Command
{
    protected static $defaultName = 'app:update-slugs';
    protected $slugGenerator;
    protected $em;
    protected $forenameRepository;
    protected $surnameRepository;

    public function __construct(string $name = null, SlugGenerator $slugGenerator, EntityManagerInterface $em, ForenameRepository $forenameRepository, SurnameRepository $surnameRepository)
    {
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        $this->forenameRepository = $forenameRepository;
        $this->surnameRepository = $surnameRepository;

        parent::__construct($name);
    }

    protected function configure()
    {
    }

    private function updateSet(array $results) {
        foreach ($results as $name) {
            $slug = $this->slugGenerator->clean($name->getLabel());
            $name->setLabels($slug);
            $name->setLabelsLength(strlen($slug));

            foreach (count_chars($slug, 1) as $i => $val) {

                $name->{chr($i)} = $val;
            }
            $this->em->persist($name);
        }
        print gc_collect_cycles()."\n";
        $this->em->flush();
        unset($results);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $continue = true;
        while ($continue) {
            print "One run [".memory_get_usage()."]\n";
            $forenames = $this->forenameRepository->findBy(['labels' => ""], [], 1000);
            $this->updateSet($forenames);
            if (sizeof($forenames) == 0) {
                print "Fin ! \n";
                $continue = false;
            }
            $this->em->clear();
            unset($forenames);
            sleep(0.5);
        }

        $continue = true;
        while ($continue) {
            print "One run [".memory_get_usage()."]\n";
            $surnames = $this->surnameRepository->findBy(['labels' => ""], [], 5000);
            $this->updateSet($surnames);
            if (sizeof($surnames) == 0) {
                print "Fin ! \n";
                $continue = false;
            }
            $this->em->clear();
            unset($surnames);
            sleep(0.5);
        }




        return 0;
    }
}
