<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParamettresController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="parametres")
     */
    public function update(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user = $this->getUser();
        $form1 = $this->createForm(RegistrationFormType::class, $user);
        $form1->handleRequest($request);

        $form2 = $this->createForm(RegistrationFormType::class, $user);
        $form2->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $data = $form1->getData();
            $user->setEmail($data['Email']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('paramettres/paramettres.html.twig', ['message' => 'Email modifié avec succès.']);
        }

        if ($form2->isSubmitted() && $form2->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form2->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('paramettres/paramettres.html.twig', ['message' => 'Email modifié avec succès.']);
        }

        return $this->render('paramettres/paramettres.html.twig', ['message' => 'Erreur lors de la modification.']);
    }
}
