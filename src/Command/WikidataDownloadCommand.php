<?php

namespace App\Command;

use App\Entity\Forename;
use App\Repository\ForenameRepository;
use App\Service\SlugGenerator;
use BorderCloud\SPARQL\SparqlClient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class WikidataDownloadCommand extends Command
{
    protected static $defaultName;

    protected $slugGenerator;
    private $em;

    public function __construct(string $name = null, SlugGenerator $slugGenerator, EntityManagerInterface $em)
    {
        $this->slugGenerator = $slugGenerator;
        $this->em = $em;
        parent::__construct($name);
    }
    protected function configure()
    {
        $this
            ->setDescription('Downloading ')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient();
        $sc->setEndpointRead($endpoint);

        $offset = 60000;
        $offsetStep = 10000;
        $continue = true;

        while ($continue) {
            print "Interrogation pour offset $offset\n";
            $addedForIteration = 0;

            $query = $this->getQuery($offset, $offsetStep);
            $rows = $sc->query($query, 'rows');

            print "# réponse OK\n";
            foreach ($rows["result"]["rows"] as $row) {
                $object = $this->getObjectFromSparqlRow($row);

                if ($object)
                {
                    $this->em->persist($object);
                    $addedForIteration++;
                }
            }


            print "# Persist OK\n";
            $this->em->flush();

            print "# Flush OK (+ $addedForIteration added)\n";
            $offset += $offsetStep;

            if ($addedForIteration == 0) {
                $continue = false;
            }
        }


        return false;
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
