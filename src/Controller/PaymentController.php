<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository): Response
    {

        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);
    
        $YOUR_DOMAIN = $_ENV['DOMAIN_URL'];

        $products_for_stripe = [];

        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser(),
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }
        
        foreach($order->getOrderDetails()->getValues() as $product){
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPricewt()*100, 0, '.', ''),
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $YOUR_DOMAIN . '/uploads/' . $product->getProductIllustration()
                        ]
                    ],
                ],
                'quantity' => $product->getProductQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($order->getCarrierPrice()*100, 0, '.', ''),
                'product_data' => [
                    'name' => 'Transporteur : ' .$order->getCarrierName(),
                ],
            ],
            'quantity' => 1,
        ];
        

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getUserIdentifier(),
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/mon-panier/annulation',
          ]);

        // On stocke l'id de la session stripe dans la commande
        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository, Cart $cart): Response
    {

        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $this->getUser(),
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        if($order->getState() == 1){
            $order->setState(2);
            $cart->remove();
            $this->entityManager->flush();
         
        }

        return $this->render('payment/success.html.twig',[
            'order' => $order
        ]);
   
    }
}
