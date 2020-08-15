<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(Utilisateur::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/editRole/{id}", name="editRole")
     * @param Utilisateur|null $user
     * @return RedirectResponse
     */
    public function editRole(Utilisateur $user = null){
        if($user == null){
            $this->addFlash('danger', "Utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            $user->setRoles(['ROLE_USER']);
        }
        else{
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash("success", 'Rôle mis à jour');
        return $this->redirectToRoute('admin');

    }
}
