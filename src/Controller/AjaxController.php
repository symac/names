<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\ResultStep;
use App\Repository\ResultRepository;
use App\Repository\ResultStepRepository;
use App\Service\PseudonameFinder;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }

    /**
     * @Route("/ajax/result/{name}", name="ajax_result")
     */
    public function result(EntityManagerInterface $em, ResultRepository $resultRepository, SlugGenerator $slugGenerator, PseudonameFinder $pseudonameFinder, string $name)
    {
        $output = [];

        $slug = $slugGenerator->clean($name);
        $result = $resultRepository->findOneBy(["slug" => $slug]);
        if (!$result) {
            $result = new Result();
            $result->setSlug($slug);
            $em->persist($result);
        }

        $forenameLengthNeeded = $result->nextForenameLengthNeeded();
        if (is_null($forenameLengthNeeded)) {
            $result->setStatus(Result::STATUS_FINISH);
            $result->setPercentageDone(100);
        } else {
            $result->setStatus(Result::STATUS_RUNNING);
            $resultStep = new ResultStep();
            $start = microtime(true);
            $anagrams = $pseudonameFinder->search($slug, $forenameLengthNeeded);
            $resultStep->setDuration(microtime(true) - $start);
            $resultStep->setAnagrams($anagrams);
            $resultStep->setForenameLength($forenameLengthNeeded);
            $resultStep->setResult($result);
            $em->persist($resultStep);
            $em->flush();
            $output["results"] = $resultStep->getAnagrams();
            $output["duration"] = $resultStep->getDuration();
        }
        $output["percent"] = $result->getPercentageDone();
        $output["status"] = $result->getStatus();
        $em->persist($result);
        $em->flush();
        return $this->json($output);
    }
}
