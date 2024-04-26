<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $adresses = $user->getAdresses();

        if (count($adresses) == 0) {
            $this->addFlash('warning', 'Vous devez ajouter une adresse pour passer commande.');
            return $this->redirectToRoute('app_account_adress_form');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'adresses' => $adresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);
        return $this->render('order/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Recapitulatif de la commande
     * 
     */
    #[Route('/commande/livraison/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart): Response
    { 

        if($request->getMethod() != 'POST'){
            return $this->redirectToRoute('app_cart');
        }
          /** @var User $user */
          $user = $this->getUser();
          $adresses = $user->getAdresses();
          
          $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'adresses' => $adresses,
            
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $adresses = $form->get('adresses')->getData();
            $delivery = $adresses->getFirstname() . ' ' . 
            $adresses->getLastname() . '<br/>' .
            $adresses->getAdresse() . '<br/>' .
            $adresses->getPostal() . ' ' . 
            $adresses->getCity() . ' - ' . 
            $adresses->getCountry();
            $delivery .= '<br/>' . $adresses->getPhone();


          $order = new Order();
            $order->setUser($user);
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($delivery);
          
          
        }

        
        foreach($products as $product){
            $orderDetail = new OrderDetail();
            $orderDetail->setProductName($product['object']->getName());
            $orderDetail->setProductIllustration($product['object']->getIllustration());
            $orderDetail->setProductPrice($product['object']->getPriceWT());
            $orderDetail->setProductTva($product['object']->getTva());
            $orderDetail->setProductQuantity($product['qty']);
            $order->addOrderDetail($orderDetail); 
         }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre commande a bien été enregistrée');    

        return $this->render('order/summary.html.twig',[
            'choices' =>  $form->getData(),
            'cart' =>  $products,
            'totalwt'=> $cart->getTotalWt(),
        ]);
    }


}
