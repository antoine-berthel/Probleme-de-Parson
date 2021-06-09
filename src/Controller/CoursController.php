<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Exercice;
use App\Entity\Inscription;
use App\Form\CreationCoursFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    /**
     * @Route("/enseignant/creer-cours", name="creer_cours")
     */
    public function creation(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $form = $this->createForm(CreationCoursFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $data = $request->request->get('creation_cours_form');

            $cours = new Cours();
            $cours->setName($data["name"]);
            $cours->setOwner($this->getUser());

            $entityManager->persist($cours);

            $entityManager->flush();

            return $this->redirect('/enseignant/get-exo?id=' . $cours->getId());
        }

        return $this->render('cours/creationCours.html.twig', [
            'creationCoursForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/enseignant/afficher-cours", name="afficher_cours")
     */
    public function afficherCours(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $repository = $this->getDoctrine()->getRepository(Cours::class);

        $cours = $repository->findBy([
            'owner' => $this->getUser()->getId(),
        ]);

        return $this->render('cours/afficherCours.html.twig', [
            'coursList' => $cours,
        ]);
    }

    /**
     * @Route("/etudiant/afficher-cours", name="afficher_cours_etu")
     */
    public function afficherCoursEtu(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $repository = $this->getDoctrine()->getRepository(Cours::class);


        $cours = $repository->findAll();

        return $this->render('cours/afficherCours.html.twig', [
            'coursList' => $cours,
        ]);
    }

    /**
     * @Route("/etudiant/afficher-mes-cours", name="afficher_mes_cours")
     */
    public function afficherMesCour(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }
        $cours = [];

        foreach ($this->getUser()->getInscriptions() as $inscription) {
            $cours[] = $inscription->getCours();
        }

        return $this->render('cours/afficherCours.html.twig', [
            'mes_cours' => true,
            'coursList' => $cours,
        ]);
    }

    /**
     * @Route("/enseignant/get-exo", name="get_exo")
     */
    public function getExercices(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);
        $Crepository = $this->getDoctrine()->getRepository(Cours::class);
        $Irepository = $this->getDoctrine()->getRepository(Inscription::class);

        $exercices = $Erepository->findBy([
            'owner' => $this->getUser()->getId(),
        ]);

        $cours = $Crepository->findOneBy([
            'id' => $request->query->get('id'),
        ]);

        $inscription = $Irepository->findBy([
            'cours' => $cours,
        ]);

        return $this->render('cours/afficherUnCours.html.twig', [
            'cours' => $cours,
            'nbInscrit' => sizeof($inscription),
            'exercices' => $exercices,
        ]);
    }

    /**
     * @Route("/enseignant/add-exo-cours", name="add_exo_cours")
     */
    public function addExercice(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);
        $Crepository = $this->getDoctrine()->getRepository(Cours::class);

        $entityManager = $this->getDoctrine()->getManager();

        $cours = $Crepository->findOneBy([
            'id' => $request->query->get('id_cours'),
        ]);

        $exercice = $Erepository->findOneBy([
            'id' => $request->query->get('id_exo'),
        ]);

        $cours->addExercice($exercice);

        $entityManager->persist($cours);

        $entityManager->flush();


        return $this->redirect('/enseignant/get-exo?id=' . $cours->getId());
    }

    /**
     * @Route("/enseignant/remove-exo-cours", name="remove_exo_cours")
     */
    public function removeExercice(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);
        $Crepository = $this->getDoctrine()->getRepository(Cours::class);

        $entityManager = $this->getDoctrine()->getManager();

        $cours = $Crepository->findOneBy([
            'id' => $request->query->get('id_cours'),
        ]);

        $exercice = $Erepository->findOneBy([
            'id' => $request->query->get('id_exo'),
        ]);

        $cours->removeExercice($exercice);

        $entityManager->persist($cours);

        $entityManager->flush();


        return $this->redirect('/enseignant/get-exo?id=' . $cours->getId());
    }

    /**
     * @Route("/enseignant/supprimer-cours", name="supprimer_cours")
     */
    public function supprimerCours(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(Cours::class);

        $cours = $repository->findOneby([
            "id" => $request->query->get('id'),
        ]);

        $entityManager->remove($cours);

        $entityManager->flush();

        return $this->redirectToRoute('afficher_cours');
    }

    /**
     * @Route("/etudiant/inscription-cours", name="inscription_cours")
     */
    public function inscriptionCours(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(Cours::class);

        $cours = $repository->findOneby([
            "id" => $request->query->get('id'),
        ]);

        foreach ($this->getUser()->getInscriptions() as $inscription) {
            if ($inscription->getCours() == $cours) {
                return $this->redirectToRoute('afficher_cours_etu');
            }
        }

        $inscription = new Inscription();

        $this->getUser()->addInscription($inscription);

        $cours->addInscription($inscription);

        $entityManager->persist($inscription);

        $entityManager->flush();

        return $this->redirectToRoute('afficher_cours_etu');
    }

    /**
     * @Route("/etudiant/desinscription-cours", name="desinscription_cours")
     */
    public function desinscriptionCours(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(Inscription::class);

        $inscription = $repository->findOneby([
            "id" => $request->query->get('id'),
        ]);

        $this->getUser()->removeInscription($inscription);

        $inscription->getCours()->removeInscription($inscription);

        $entityManager->remove($inscription);

        $entityManager->flush();

        return $this->redirectToRoute('afficher_mes_cours');
    }

    /**
     * @Route("/enseignant/get-exo-etu", name="get_exo_etu")
     */
    public function getExercicesEtu(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $repository = $this->getDoctrine()->getRepository(Cours::class);
        $Irepository = $this->getDoctrine()->getRepository(Inscription::class);

        $cours = $repository->findOneBy([
            'id' => $request->query->get('id'),
        ]);

        $inscription = $Irepository->findOneBy([
            'cours' => $cours,
            'etudiant' => $this->getUser(),
        ]);

        if (sizeof($cours->getExercices()) == 0) {
            return $this->redirectToRoute('afficher_mes_cours');
        }

        return $this->render('cours/coursEtu.html.twig', [
            'cours' => $cours,
            'exercice' => $cours->getExercices()[$request->query->get('nb') % (sizeof($cours->getExercices()) == 0 ? 1 : sizeof($cours->getExercices()))],
            'courent' => $request->query->get('nb'),
            'inscription' => $this->getUser()->getInscriptions(),
            'done' => $inscription->getDoneExercices(),
        ]);
    }
}
