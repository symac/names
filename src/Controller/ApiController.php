<?php

namespace App\Controller;

use App\Entity\Result;
use App\Repository\ResultRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/get/{query}", name="apiget")
     */
    public function getResult($query, ResultRepository $resultRepository, SlugGenerator $slugGenerator, EntityManagerInterface $em) {
        $slug = $slugGenerator->clean($query);
        $result = $resultRepository->findOneBy(['slug' => $slug]);
        if (!$result) {
            $result = new Result();
            $result->setSlug($slug);
            $result->setLengthProcessed(0);
        }

        $em->persist($result);
        $em->flush();
        dd($query);
        exit;

        $response = new JsonResponse(['data' => 123]);

        return $response;
    }
}
