<?php

namespace App\Controller;

use App\Entity\Forename;
use App\Entity\Result;
use App\Entity\Surname;
use App\Form\SearchType;
use App\Repository\ResultRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, SlugGenerator $slugGenerator, ResultRepository $resultRepository, EntityManagerInterface $em)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $searchName = $task["search_name"];
            $slugName = $slugGenerator->clean($searchName);
            $result = $resultRepository->findOneBy(["slug" => $slugName]);

            if ((!is_null($result)) && ($result->getStatus() == Result::STATUS_FINISH)) {
                return $this->render('index/result.html.twig', [
                    'search_name' => $task["search_name"],
                    'result' => $result
                ]);
            }

            if (is_null($result)) {
                $result = new Result();
                $result->setSlug($slugName);
                $result->setSearch($searchName);
                $em->persist($result);
                $em->flush();
            }
            return $this->render('index/result.html.twig', [
                'result' => $result
            ]);

        }
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/anagrams-of-{name}/{id}", name="permalink")
     */
    public function permalink(Result $result, string $name)
    {
        if ($result->getSearchSlugified() != $name) {
            throw $this->createNotFoundException('The product does not exist');
        }
        return $this->render('index/result.html.twig', [
            'result' => $result
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(EntityManagerInterface $em)
    {
        $countForenames = $em->getRepository(Forename::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $countSurnames = $em->getRepository(Surname::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $countAnagrams = $em->getRepository(Result::class)->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        return $this->render('index/about.html.twig', [
            "countSurnames" => $countSurnames,
            "countForenames" => $countForenames,
            "countAnagrams" => $countAnagrams
        ]);
    }
}
