<?php

namespace App\Controller;

use App\Entity\Forename;
use App\Entity\Result;
use App\Entity\Surname;
use App\Form\SearchType;
use App\Repository\QuizzRepository;
use App\Repository\ResultRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(Request $request, SlugGenerator $slugGenerator, ResultRepository $resultRepository, EntityManagerInterface $em, QuizzRepository $quizzRepository)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $searchName = $task["search_name"];
            $result = $resultRepository->findOneBy(["search" => $searchName]);

            if ((!is_null($result)) && ($result->getStatus() == Result::STATUS_FINISH)) {
                $dt = new \DateTime();
                # print "Compare #.$result->getViewDate()."# -- #".$dt
                if ($result->getViewDate()->format("Y-m-d") != $dt->format("Y-m-d")) {
                    $result->setViewDate(new \DateTime());
                    $em->persist($result);
                    $em->flush();
                }

                return $this->render('index/result.html.twig', [
                    'search_name' => $task["search_name"],
                    'result' => $result,
                    'form' => $form->createView()
                ]);
            }

            if (is_null($result)) {
                $result = new Result($slugGenerator);
                $result->setSearch($searchName);
                $em->persist($result);
                $em->flush();
            }
            return $this->render('index/result.html.twig', [
                'result' => $result,
                'form' => $form->createView()
            ]);

        }

        // Getting a quizz
        // $quizz = $quizzRepository->findRandom();
        $quizz = null;
        
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
            'quizz' => $quizz
        ]);
    }

    #[Route(path: '/anagrams-of-{name}/{id}', name: 'permalink')]
    public function permalink(Result $result, string $name)
    {
        $form = $this->createForm(SearchType::class);

        if ($result->getSearchSlugified() != $name) {
            throw $this->createNotFoundException('Erreur dans l\'adresse');
        }
        return $this->render('index/result.html.twig', [
            'result' => $result,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/about', name: 'about')]
    public function about(EntityManagerInterface $em)
    {
        $form = $this->createForm(SearchType::class);
        $countForenames = $em->getRepository(Forename::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $countSurnames = $em->getRepository(Surname::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $countAnagrams = $em->getRepository(Result::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        return $this->render('index/about.html.twig', [
            "countSurnames" => $countSurnames,
            "countForenames" => $countForenames,
            "countAnagrams" => $countAnagrams,
            "form" => $form->createView()
        ]);
    }
}
