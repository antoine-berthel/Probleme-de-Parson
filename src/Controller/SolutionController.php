<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Entity\Solution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SolutionController extends AbstractController
{
    /**
     * @Route("/enseignant/supprimer-solution", name="supprimer_solution")
     */
    public function supprimer(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(Solution::class);

        $solution = $repository->findOneby([
            "id" => $request->query->get('id'),
        ]);

        $exercice = $solution->getExercice();

        $entityManager->remove($solution);

        $entityManager->flush();

        return $this->redirect('/enseignant/afficher-exercice?id=' . $exercice->getId());
    }

    /**
     * @Route("/get-solution", name="get_solution")
     */
    public function getSolution(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $Srepository = $this->getDoctrine()->getRepository(Solution::class);
        $Erepository = $this->getDoctrine()->getRepository(Exercice::class);

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ENSEIGNANT')) {
            if ($request->isXmlHttpRequest()) {

                $solution = $Srepository->findOneby([
                    "id" => $request->query->get('id'),
                ]);

                return new JsonResponse($solution->getSolution());
            }
        }

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ETUDIANT')) {
            $entityManager = $this->getDoctrine()->getManager();

            $exercice = $Erepository->findOneby([
                "id" => $request->request->get('id'),
            ]);

            $solutions = $Srepository->findby([
                "exercice" => $exercice,
            ]);

            $proposition = $request->request->get('proposition');

            foreach ($solutions as $sol) {
                if (array_diff($sol->getSolution(), $proposition) == []) {
                    foreach ($this->getUser()->getInscriptions() as $inscription) {
                        if (!$inscription->isDoneExercice($exercice)) {
                            if ($inscription->getCours()->getId() == $request->request->get('cours')) {
                                $exercice->setSuccess($exercice->getSuccess() + 1);
                                $inscription->addDoneExercice($exercice);
                                $entityManager->persist($inscription);
                                $entityManager->flush();
                                return new JsonResponse(true);
                            }
                        } else {
                            return new JsonResponse(true);
                        }
                    }
                }
            }
            return new JsonResponse(false);
        }
    }
}
