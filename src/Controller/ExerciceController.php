<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\Inscription;
use App\Entity\Solution;
use App\Form\CreationExerciceFormType;
use App\Form\CreationSolutionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciceController extends AbstractController
{
    /**
     * @Route("/enseignant/creer-exercice", name="creer_exercice")
     */
    public function creation(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $repository = $this->getDoctrine()->getRepository(Exercice::class);
        $form = $this->createForm(CreationExerciceFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $data = $request->request->get('creation_exercice_form');

            $parson = $repository->findOneBy([
                'name' => $data['name'],
                'owner' => $this->getUser()->getId(),
            ]);

            if ($parson == null) {
                $parson = new Exercice();
                $parson->setOwner($this->getUser());
                $parson->setSuccess(0);
                $parson->setTry(0);
                $parson->setName($data['name']);
                $parson->setDescription($data['description']);
                $parson->setProblem($data['problem']);

                $entityManager->persist($parson);
            }

            $entityManager->flush();

            return $this->redirect('/enseignant/afficher-exercice?id=' . $parson->getId());
        }

        return $this->render('exercice/creationExercice.html.twig', [
            'creationExerciceForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/enseignant/afficher-exercice", name="afficher_exercice")
     */
    public function afficherExercice(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $Srepository = $this->getDoctrine()->getRepository(Solution::class);
        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);

        $form = $this->createForm(CreationSolutionFormType::class);

        $exercice = $Erepository->findOneBy([
            'id' => $request->query->get('id'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $data = $request->request->get('creation_solution_form');

            if ($exercice != null) {
                $solution = new Solution();
                $solution->setExercice($exercice);
                $solution->setSolution($data['solution']);
                $entityManager->persist($solution);
                $entityManager->flush();
            }
        }

        $solutions = $Srepository->findby([
            "exercice" => $exercice,
        ]);

        return $this->render('exercice/afficherExercice.html.twig', [
            'exercice' => $exercice,
            'solutions' => $solutions,
            'creationSolutionForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/enseignant/afficher-exercices", name="afficher_exercices")
     */
    public function afficherExercices(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $repository = $this->getDoctrine()->getRepository(Exercice::class);
        $Irepository = $this->getDoctrine()->getRepository(Inscription::class);

        $exercices = $repository->findBy([
            'owner' => $this->getUser()->getId(),
        ]);

        return $this->render('exercice/afficherExercices.html.twig', [
            'exerciceList' => $exercices,
        ]);
    }

    /**
     * @Route("/enseignant/supprimer-exercice", name="supprimer_exercice")
     */
    public function supprimer(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);
        $Srepository = $this->getDoctrine()->getRepository(Solution::class);

        $exercice = $Erepository->findOneby([
            "owner" => $this->getUser()->getId(),
            "id" => $request->query->get('id'),
        ]);

        $solutions = $Srepository->findby([
            "exercice" => $exercice,
        ]);

        $entityManager->remove($exercice);

        foreach ($solutions as $solution) {
            $entityManager->remove($solution);
        }

        $entityManager->flush();

        return $this->redirectToRoute('afficher_exercices');
    }
}
