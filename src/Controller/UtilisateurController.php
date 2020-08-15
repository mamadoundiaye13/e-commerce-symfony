<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="utilisateur")
     */
    public function index()
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    /**
     * @Route("/etudiant/edit/{id}", name="edit_etudiant")
     * @param Utilisateur|null $user
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Utilisateur $user = null, Request $request){
        if($user == null){
            $this->addFlash('danger', 'Etudiant introuvable');

            return $this->redirectToRoute('produit_index');
        }

        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Etudiant mis à jour');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);

    }
}
