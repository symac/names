<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Entity\Result;
use App\Form\QuizzType;
use App\Repository\QuizzRepository;
use App\Repository\ResultRepository;
use App\Service\SlugGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Firewall\RemoteUserAuthenticationListener;

class QuizzController extends AbstractController
{
    /**
     * @Route("/quizz", name="quizz")
     */
    public function index()
    {
        return $this->render('quizz/index.html.twig', [
            'controller_name' => 'QuizzController',
        ]);
    }

    /**
     * @Route("/quizz/add", name="quizz_add")
     */
    public function add(Request $request, EntityManagerInterface $em) {

        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class, $quizz);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $quizz = $form->getData();
            $em->persist($quizz);
            $em->flush();

            return $this->redirect($this->generateUrl("quizz_add_second", ["id" => $quizz->getId(), "secret" => $quizz->getSecret()]));
        }

        return $this->render('quizz/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/quizz/add/{id}-{secret}", name="quizz_add_second")
     */
    public function addSecond(Quizz $quizz, string $secret, ResultRepository $resultRepository, SlugGenerator $slugGenerator) {
        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='".$this->generateUrl("home")."'>homepage</a>");
        }

        $result = $resultRepository->findOneBy(["search" => $quizz->getAnswer()]);
        if (is_null($result)) {
            $result = new Result($slugGenerator);
            $result->setSearch($quizz->getAnswer());
        }
        return $this->render("quizz/add_select_answer.html.twig", [
            "quizz" => $quizz,
            "result" => $result
        ]);
    }

    /**
     * @Route("/quizz/add/{id}-{secret}/{anagram}", name="quizz_add_final")
     */
    public function addThird(Quizz $quizz, string $secret, string $anagram, EntityManagerInterface $em) {

        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='".$this->generateUrl("home")."'>homepage</a>");
        }

        $quizz->setAnagram($anagram);
        $em->persist($quizz);
        $em->flush();

        return $this->redirect(
            $this->generateUrl("quizz_view", [
                "id" => $quizz->getId(),
                "secret" => $quizz->getSecret()
            ])
        );
    }

    /**
     * @Route("/quizz/{secret}{id}", name="quizz_view", requirements={"secret"=".{10}"})
     */
    public function view(Quizz $quizz, string $secret) {
        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='".$this->generateUrl("home")."'>homepage</a>");
        }

        return $this->render("quizz/view.html.twig", [
            "quizz" => $quizz
        ]);
    }

    /**
     * @Route("/quizz/random", name="quizz_random")
     */
    public function random(QuizzRepository $quizzRepository) {
        $quizz = $quizzRepository->getRandom();
        return $this->redirect(
            $this->generateUrl("quizz_view", ["id" => $quizz->getId(), "secret" => $quizz->getSecret()])
        );
    }
}
