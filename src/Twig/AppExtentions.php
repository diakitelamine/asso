<?php

namespace App\Twig;

use App\Classe\Cart;
use App\Repository\CategoryRepository;
use Twig\TwigFilter;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{


    private $categoryRepository;
    private $cart;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }

    /**
     * Fonction pour ajouter des filtres à Twig
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }
    
    /**
     * Fonction pour formater le prix
     *
     * @param [type] $number
     * @return string
     */
    public function formatPrice($number): string
    {
     
        return number_format($number, 2, ',', ' ').' €';

    }

    /**
     * Fonction pour ajouter des variables globales à Twig
     * @inheritDoc
     */
    public function getGlobals(): array
    {
        return [
       
            'allCategories' => $this->categoryRepository->findAll(),
            'fullQuantity'=> $this->cart->fullQuantity()
        ];
    }
}