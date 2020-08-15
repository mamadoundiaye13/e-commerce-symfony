<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\ContenuPanierRepository;
use App\Repository\PanierRepository;
use App\Service\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     * @param PanierService $panierService
     * @return Response
     */
    public function index(PanierService $panierService)
    {
        return $this->render('panier/index.html.twig', [
            'items' => $panierService->getFullPanier(),
            'total' => $panierService->getTotal()
        ]);
    }

    /**
     * @Route ("/panier/add/{id}", name="panier_add")
     * @param $id
     * @param PanierService $panierService
     * @return RedirectResponse
     */

    public function add($id, PanierService $panierService)
    {
        $panierService->add($id);
        $this->addFlash('success', 'Le produit est bien ajouté dans le panier');
        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     * @param $id
     * @param PanierService $panierService
     * @return RedirectResponse
     */
    public function remove($id, PanierService $panierService){
       $panierService->remove($id);

        $this->addFlash('error', 'Le produit est bien supprimé dans le panier');
        return $this->redirectToRoute('panier');

    }

    /**
     * @Route("/panier/achat", name="panier_achat")
     * @param PanierService $panierService
     * @return RedirectResponse
     */
    public function achat(PanierService $panierService){
        $panier = new Panier();
        $em = $this->getDoctrine()->getManager();

        foreach ($panierService->getFullPanier() as $item){
            $contenuPanier = new ContenuPanier();
            $contenuPanier->setPanier($panier);
            $contenuPanier->setQuantite($item['quantite']);
            $contenuPanier->setProduit($item['produit']);
            $em->persist($contenuPanier);

        }
        $panier->setUtilisateur($this->getUser());
        $panier->setEtat(true);
        $em->persist($panier);
        $em->flush();
        $this->addFlash('success', 'panier bien acheté');

        $panierService->clear();

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/commande", name="panier_commande")
     * @param PanierRepository $panierRepository
     * @param ContenuPanierRepository $contenuPanierRepository
     * @return Response
     */
    public function commande(PanierRepository $panierRepository, ContenuPanierRepository $contenuPanierRepository){

        return $this->render('panier/commande.html.twig', [

            'commandes' => $panierRepository->findBy(
                ['Etat' => 1, 'Utilisateur' => $this->getUser()],
                ['Date_achat' => 'DESC']
            ),

        ]);
    }

    /**
     * @Route("/commande/edit/{id}", name="edit_commande")
     * @param Request $request
     * @param Panier $commande
     * @return Response
     */

    public function editCommande(Request $request, Panier $commande): Response
    {
        $form = $this->createForm(PanierType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_commande');
        }

        return $this->render('panier/edit-commande.html.twig', [
            'commande' => $commande,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("panier/delete/{id}", name="commande_delete", methods={"DELETE"})
     * @param Request $request
     * @param Panier $commande
     * @return Response
     */
    public function delete(Request $request, Panier $commande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_commande');
    }
}
