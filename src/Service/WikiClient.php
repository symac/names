<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Json;

class WikiClient
{
    public function __construct(EntityManagerInterface $em, SlugGenerator $slugGenerator)
    {
    }

    public function getWikidataJson(string $Q) {
        try {
            $content = file_get_contents("https://www.wikidata.org/w/api.php?action=wbgetentities&ids=".$Q."&props=claims|labels|descriptions&format=json");
            $json = json_decode($content);
            if ($json->{'success'} != 1) {
                return null;
            }
            return $json;
        } catch (\Exception $e) {
            return null;
        }
        return null;

    }

    public function extractImageName($json, $Q) {
        try {
            $image = $json->{'entities'}->{$Q}->{'claims'}->{'P18'}[0]->{'mainsnak'}->{'datavalue'}->{'value'};
            $image = str_replace(" ", "_", $image);
            return $image;
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function extractLabel($json, $Q) {
        try {
            $label = $json->{'entities'}->{$Q}->{'labels'}->{'en'}->{"value"};
            return $label;
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function extractDescription($json, $Q) {
        try {
            $description = $json->{'entities'}->{$Q}->{'descriptions'}->{'en'}->{"value"};
            return $description;
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    public function cleanQ($wikidata) {
        return preg_replace("#https?://www.wikidata.org/entity/#", "", $wikidata);
    }

    public function getThumbnailUrl($json, $Q) {
        $originalFilename = $this->extractImageName($json, $Q);
        if (!is_null($originalFilename)) {
            $extension = preg_replace("/^.*\.(.*)$/", "$1", $originalFilename);
            $apfFilename = $Q.".".$extension;
            $url_thumb = "https://commons.wikimedia.org/w/thumb.php?f=".$originalFilename."&w=200";
            file_put_contents(__DIR__."/../../public/images/quizz/".$apfFilename, file_get_contents($url_thumb));
            return $apfFilename;
        }
    }
}