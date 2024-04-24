<?php

namespace App\Classe;


use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {
        
    }

    /**
     * Fonction pour ajouter un produit au panier
     */

    public function add(Product $product): void
    {
        // Récupérer la session
        $session = $this->requestStack->getSession();

        // Récupérer le panier de la session s'il existe, sinon initialiser un tableau vide
        $cart = $this->getCart();

        // Vérifier si le produit est déjà dans le panier
        if (array_key_exists($product->getId(), $cart)) {
            // Si le produit est déjà dans le panier, augmenter la quantité
            $cart[$product->getId()]['qty']++;
        } else {
            // Sinon, ajouter le produit au panier avec une quantité de 1
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);
    }

    /**
     * Fonction pour récupérer le panier
     */

    public function getCart(): array
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    /**
     *  Fonction pour récupérer la quantité totale des produits dans le panier
     */
    public function fullQuantity(): float
    {
        $cart = $this->getCart();
        $quantity = 0;

        if (!isset(($cart))) {
            return $quantity;
        }

        foreach ($cart as $product) {
            $quantity += $product['qty'];
        }
       return $quantity;
    }

    /**
     * Fonction pour récupérer le prix total TTC du panier
     */
    public function getTotalWt(): float
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset(($cart))) {
            return $price;  
        }

         // Calculer le prix total TTC du panier 
        foreach ($cart as $product) {
            $price = $price + ($product['object']->getPriceWT() * $product['qty']);
        }

       return $price;
    }


    /**
     * Fonction pourvider le panier
     *
     * @return void
     */
    public function remove(): void
    {
        // Récupérer la session
        $session = $this->requestStack->getSession();
        // Vider le panier
        $cart = [];

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);
    }

    /**
     * Fonction pour diminuer la quantité d'un produit dans le panier
     *
     * @param int $id
     * @return void
     */
    public function decrease($id): void
    {
        // Récupérer la session
        $session = $this->requestStack->getSession();

        // Récupérer le panier de la session s'il existe, sinon initialiser un tableau vide
        $cart = $this->getCart();

        // Vérifier si le produit est déjà dans le panier
        if (array_key_exists($id, $cart)) {
            // Si la quantité est supérieure à 1, diminuer la quantité
            if ($cart[$id]['qty'] > 1) {
                $cart[$id]['qty']--;
            } else {
                // Sinon, supprimer le produit du panier
                unset($cart[$id]);
            }
        }

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);
    }
}
