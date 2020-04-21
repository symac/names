<?php

namespace App\Controller;

use App\Entity\Result;
use App\Entity\ResultStep;
use App\Repository\QuizzRepository;
use App\Repository\ResultRepository;
use App\Repository\ResultStepRepository;
use App\Service\PseudonameFinder;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $result = $resultRepository->findOneBy(["search" => $name]);
        if (!$result) {
            $result = new Result($slugGenerator);
            $result->setSearch($name);
            $em->persist($result);
        }

        $forenameLengthNeeded = $result->nextForenameLengthNeeded();
        if (is_null($forenameLengthNeeded)) {
            $result->setStatus(Result::STATUS_FINISH);
            $result->setPercentageDone(100);
        } else {
            $result->setStatus(Result::STATUS_RUNNING);
            $start = microtime(true);
            $anagrams = $pseudonameFinder->search($result->getSlug(), $forenameLengthNeeded);
            $duration = microtime(true) - $start;
            $resultStep = $result->addAnagrams($anagrams, $forenameLengthNeeded, $duration);
            $em->persist($resultStep);
            $em->persist($result);
            $em->flush();
            $output["results"] = $anagrams;
            $output["duration"] = $duration;
        }

        $output["percent"] = $result->getPercentageDone();
        $output["status"] = $result->getStatus();
        $output["totalCount"] = $result->getCountAnagrams();

        $em->persist($result);
        $em->flush();
        return $this->json($output);
    }

    /**
     * @Route("/ajax/quizz", name="ajax_random_quizz")
    */
    public function quizz(QuizzRepository $quizzRepository) {
        $quizz = $quizzRepository->findRandom();
        return new JsonResponse($quizz);
        dd($quizz);
    }
}
