<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: ['motif' => null])]
    public function index(Cart $cart, $motif): Response
    {

        if ($motif == 'annulation') {
            $this->addFlash('info', 'Paiement annulé : Vous pouvez mettre à jour votre panier.');
        }
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt(),
        ]);
    }


    #[Route('/card/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {

        // Récupérer le produit
        $produit = $productRepository->findOneById($id);
        $cart->add($produit);

        $this->addFlash('success', 'Le produit a bien été ajouté à votre panier.');

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/card/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {

        // Récupérer le produit
        $cart->decrease($id);

        $this->addFlash('success', 'La quantité du produit a bien été diminuée.');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/mon-panier/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {

        //vide le panier
        $cart->remove();

        $this->addFlash('success', 'Le panier a bien été vidé.');
     
        return $this->redirectToRoute('app_home');
    }

    
}
