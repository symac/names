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

class ExportSlugsCommand extends Command
{
    protected static $defaultName = 'app:export-slugs';
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
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    private function buildQuery($surnameId) {
        $tableSurname = $this->em->getClassMetadata(Forename::class)->getTableName();
        $tableForname = $this->em->getClassMetadata(Surname::class)->getTableName();

        $query = "select  as surnameId, A.id as surnameId, B.label as forename from $tableSurname as A, $tableForname as B WHERE ";
        $lettersSlug = str_split($slug);
        $lettersSlug = array_unique($lettersSlug);
        $lettersNotSlug = [];
        foreach (range('A', 'Z') as $letter) {
            if (!in_array($letter, $lettersSlug)) {
                array_push($lettersNotSlug, $letter);
            }
        }

        foreach ($lettersNotSlug as $letter) {
            $query .= "(A.labels not like '%$letter%' and B.labels not like '%$letter%') AND \n";
        }

        foreach ($lettersSlug as $letter) {
            $query .= "(A.labels like '%$letter%' or B.labels like '%$letter%') AND \n";
        }

        $query = preg_replace("/ AND \n$/", "", $query);
        # $query .= "(A.labels_length + B.labels_length) = ".strlen($slug);
        return $query;
    }

    public function exportSlugForForename(Forename $forename) {
        $surnames = $this->surnameRepository->findBy([], [], 100);
        $surnames = $this->surnameRepository->findAll();

        $output_file_name = __DIR__."/../../data/slugs_by_forename/".sprintf("%07d.txt", $forename->getId());
        print $output_file_name."\n";
        $output_file = fopen($output_file_name, "w");

        print "Début écriture $output_file_name\n";
        foreach ($surnames as $surname) {
            $fullSlug = $this->slugGenerator->clean($surname->getLabel().$forename->getLabel());
            $checksum = crc32($fullSlug);
            $output_line = sprintf("%s;%s;%s;%s\n", $fullSlug, $forename->getId(), $surname->getId(), $checksum);
            fputs($output_file, $output_line);
        }
        print "Fin écriture\n";
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $forenames = $this->forenameRepository->findBy(["exported" => 0]);
        foreach ($forenames as $forename) {
            $this->exportSlugForForename($forename);
            $forename->setExported(true);
            $this->em->persist($forename);
            $this->em->flush();
            exit;
        }
        exit;

        exit;
        $start = microtime(true);
        $name = "Sylvain Lovelace";
        $name = "Sylvain Lovelace";
        $slug = $this->slugGenerator->clean($name);
        $query = $this->buildQuery($slug);

        $stmt = $this->em->getConnection()->prepare($query." LIMIT 0,10000");
        $stmt->execute();

        $results = $stmt->fetchAll(2);
        $resultsNumber = sizeof($results);
        foreach ($results as $result) {
            $suggestion = $result["surname"]." ".$result["forename"];
            $slugCombined = $this->slugGenerator->clean($suggestion);

            if ($slugCombined == $slug) {
                //dd($result);
                // print $suggestion."\n";
            }
        }

        print "Time taken : ".(microtime(true) - $start)." : $resultsNumber resultats\n";
        exit;


        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
