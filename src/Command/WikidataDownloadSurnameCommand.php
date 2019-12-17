<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WikidataDownloadSurnameCommand extends WikidataDownloadCommand
{
    protected static $defaultName = 'app:wikidata-download-surname';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function getQuery($offset, $offsetStep) {
        $query = "SELECT ?surname ?surnameLabel
            WHERE
            {
              ?surname wdt:P31 wd:Q101352.
              SERVICE wikibase:label { bd:serviceParam wikibase:language \"fr,en,de,es\". }
            }
            LIMIT $offsetStep
            OFFSET $offset
            ";
        return $query;
    }


    protected function getObjectFromSparqlRow($row) {
        $surname = new Surname();
        $surname->setLabel($row["surnameLabel"]);
        $surname->setLabels($this->slugGenerator->clean($surname->getLabel()));
        $surname->setQ(str_replace("http://www.wikidata.org/entity/", "", $row["surname"]));

        if ($surname->getLabels() != "") {
            return $surname;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output); // TODO: Change the autogenerated stub
    }
}
