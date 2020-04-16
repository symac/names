<?php

namespace App\Command;

use App\Entity\Forename;
use App\Entity\Surname;
use App\Service\SlugGenerator;
use BorderCloud\SPARQL\SparqlClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use EasyRdf_Parser_Exception;

class GetGendersCommand extends Command
{
    protected static $defaultName = 'app:get-genders';

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
            ->setDescription('Getting genders ');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $endpoint = "https://query.wikidata.org/sparql";
        $sc = new SparqlClient(true);
        $sc->setEndpointRead($endpoint);

        $query = "SELECT ?forename ?forenameType
WHERE
{
  ?forename wdt:P31/wdt:P279* wd:Q202444 .
  ?forename wdt:P31 ?forenameType .
} LIMIT 10";
        ini_set('memory_limit', '4000M');
        // $rows = $sc->query($query, 'json' );
        // Setup some additional prefixes for DBpedia
        \EasyRdf_Namespace::set('category', 'http://dbpedia.org/resource/Category:');
        \EasyRdf_Namespace::set('dbpedia', 'http://dbpedia.org/resource/');
        \EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
        \EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

        $sparql = new \EasyRdf_Sparql_Client('https://query.wikidata.org/sparql');

        $result = $sparql->query(
            'SELECT * WHERE {'.
            '  ?country rdf:type dbo:Country .'.
            '  ?country rdfs:label ?label .'.
            '  ?country dc:subject category:Member_states_of_the_United_Nations .'.
            '  FILTER ( lang(?label) = "en" )'.
            '} ORDER BY ?label'
        );
        foreach ($result as $row) {
            echo "<li>".link_to($row->label, $row->country)."</li>\n";
        }
        dd($result);
        exit;
        
        $err = $sc->getErrors();
        if ($err) {
            print_r($err);
            throw new Exception(print_r($err, true));
        }
        dd($rows);
        print "OK";
        exit;
        dd($rows);
        dd(sizeof($rows["result"]["rows"]));
        return 1;
    }
}
