<?php

namespace App\Service\Panier;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class  PanierService {

    protected $session;
    protected $produitRepository;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository){
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    public function add(int $id){
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])){
            $panier[$id]++;
        }
        else{
            $panier[$id] = 1;
        }


        $this->session->set('panier', $panier);
    }

    public function remove(int $id){
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])){
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function clear(){
        $this->session->set('panier', []);
    }

    public function getFullPanier(): array
    {
        $panier = $this->session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity){
            $panierWithData[] = [
                'produit' => $this->produitRepository->find($id),
                'quantite' => $quantity
            ];
        }

        return $panierWithData;

    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getFullPanier() as $item){
            $total += $item['produit']->getPrix() * $item['quantite'];
        }

        return $total;
    }

}
