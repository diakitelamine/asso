<?php

namespace App\Controller;

use Dompdf\Dompdf;

use App\Repository\OrderRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceController extends AbstractController
{

    /**
     * Impression de la facture pour un utilisateur connecté
     * Verificaton de la commande pour un utilisateur donné
     *
     * @return void
     */
    #[Route('/compte/facture/impression/{id_order}', name: 'app_invoice')]
    public function index($id_order, OrderRepository $orderRepository)
    {
        $order = $orderRepository->findOneById(['id' => $id_order]);

        if (!$order) {
            return $this->redirectToRoute('app_account');
        }

        if ($order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account');
        }

    

        $dompdf = new Dompdf();

        $html = $this->renderView('invoice/index.html.twig', [
            'order' => $order
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream( 'facture.pdf', [
            'Attachment' => false
        ]);

       exit();
        
    }
}
