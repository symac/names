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
     * @Route("/quizz/add", name="quizz_add_01")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $quizz = new Quizz();
        $form = $this->createForm(QuizzType::class, $quizz);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $quizz = $form->getData();
            $em->persist($quizz);
            $em->flush();

            return $this->redirect($this->generateUrl("quizz_add_02_image_anagram", ["id" => $quizz->getId(), "secret" => $quizz->getSecret()]));
        }

        return $this->render('quizz/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function getImageFromWikidata(string $Q)
    {
        try {
            $content = file_get_contents("https://www.wikidata.org/w/api.php?action=wbgetentities&ids=".$Q."&props=claims&format=json");
            $json = json_decode($content);
            if ($json->{'success'} != 1) {
                return null;
            }
            $image = $json->{'entities'}->{$Q}->{'claims'}->{'P18'}[0]->{'mainsnak'}->{'datavalue'}->{'value'};
            $image = str_replace(" ", "_", $image);
            return $image;
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    /**
     * @Route("/quizz/add/{id}-{secret}", name="quizz_add_02_image_anagram")
     */
    public function addSecond(Quizz $quizz, string $secret, ResultRepository $resultRepository, SlugGenerator $slugGenerator, EntityManagerInterface $em)
    {
        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='" . $this->generateUrl("home") . "'>homepage</a>");
        }

        // On va récupérer l'image
        $commonsImage = $this->getImageFromWikidata($quizz->getWikidata());
        if (!is_null($commonsImage)) {
            $url_thumb = "https://commons.wikimedia.org/w/thumb.php?f=".$commonsImage."&w=200";
            file_put_contents(__DIR__."/../../public/images/quizz/".$quizz->imageFilename(), file_get_contents($url_thumb));
            $quizz->setImage($quizz->imageFilename());
            $em->persist($quizz);
            $em->flush();
        }

        //https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q17636011&properties=claims&format=json

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
     * @Route("/quizz/add/{id}-{secret}/finalize", name="quizz_add_final")
     */
    public function addFinal(Quizz $quizz, string $secret, EntityManagerInterface $em, Request $request, SlugGenerator $slugGenerator)
    {

        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='" . $this->generateUrl("home") . "'>homepage</a>");
        }


        $anagram = $request->get("anagram");
        if ($slugGenerator->clean($anagram) != $slugGenerator->clean($quizz->getAnswer())) {
            return new  Response("There is an issue with the url, please return to <a href='" . $this->generateUrl("home") . "'>homepage</a>");
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
    public function view(Quizz $quizz, string $secret)
    {
        if ($quizz->getSecret() != $secret) {
            return new  Response("There is an issue with the url, please return to <a href='" . $this->generateUrl("home") . "'>homepage</a>");
        }

        return $this->render("quizz/view.html.twig", [
            "quizz" => $quizz
        ]);
    }

    /**
     * @Route("/quizz/random", name="quizz_random")
     */
    public function random(QuizzRepository $quizzRepository)
    {
        $quizz = $quizzRepository->getRandom();
        return $this->redirect(
            $this->generateUrl("quizz_view", ["id" => $quizz->getId(), "secret" => $quizz->getSecret()])
        );
    }

    /**
     * @Route("/quizz/setAnagram/{id?}/{anagram?}", name="quizz_set_anagram")
     */
    public function setAnagram(SlugGenerator $slugGenerator, QuizzRepository $quizzRepository, EntityManagerInterface $em, ResultRepository $resultRepository, Quizz $quizz = null, string $anagram = null)
    {
        if (!is_null($quizz)) {
            $quizz->setAnagram($anagram);
            $em->persist($quizz);
            $em->flush();
        }
        $quizz = $quizzRepository->findNeedAnagram();

        $result = $resultRepository->findOneBy(["search" => $quizz->getAnswer()]);
        if (is_null($result)) {
            $result = new Result($slugGenerator);
            $result->setSearch($quizz->getAnswer());
            $em->persist($result);
            $em->flush();
        }

        return $this->render("quizz/select_answer.html.twig", [
            "quizz" => $quizz,
            "result" => $result

        ]);
    }

    /**
     * @Route("quizz/delete/{id}", name="quizz_delete")
     */
    public function delete(Quizz $quizz, EntityManagerInterface $em) {
        $em->remove($quizz);
        $em->flush();
        return $this->redirect(
          $this->generateUrl("quizz_set_anagram")
        );
    }

}
