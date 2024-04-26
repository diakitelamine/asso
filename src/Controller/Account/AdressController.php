<?php


namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Adress;
use App\Form\AdressUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdressController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
   /**
     * Fontion permettant de lister les adresses de l'utilisateur
     *
     * @return Response
     */
    #[Route('/compte/adresses', name: 'app_account_adresses')]
    public function index(): Response
    {
        return $this->render('account/adress/index.html.twig');
    }

   #[Route('/compte/adresses/delete/{id}', name: 'app_account_remove_adress')]
    public function delete($id): Response
    {
        $adress = $this->entityManager->getRepository(Adress::class)->findOneById($id);
        if($adress && $adress->getUser() == $this->getUser()){
            $this->entityManager->remove($adress);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre adresse a bien été supprimée');
        }
        return $this->redirectToRoute('app_account_adresses');
    }

    /**
     * Fonction permettant d'ajouter ou de modifier une adresse
     *
     * @param Request $request
     * @param [type] $id
     * @return Response
     */
    #[Route('/compte/adress/ajouter/{id}', name: 'app_account_adress_form', defaults: ['id' => null])]
    public function form(Request $request, $id, Cart $cart): Response
    {
        if($id){
              $adresse = $this->entityManager->getRepository(Adress::class)->findOneById($id);
              if(!$adresse || $adresse->getUser() != $this->getUser()){
                  return $this->redirectToRoute('app_account_adresses');
              }
        } else {  
        
            $adresse = new Adress();
            $adresse->setUser($this->getUser());
        }

        $form = $this->createForm(AdressUserType::class, $adresse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            if($id){
                $this->addFlash('success', 'Votre adresse a bien été modifiée');
            } else {
                $this->addFlash('success', 'Votre adresse a bien été ajoutée');
            }

            if($cart->fullQuantity()>0) {
                return $this->redirectToRoute('app_order');
            }
            

            return $this->redirectToRoute('app_account_adresses');
        }

        return $this->render('account/adress/form.html.twig',[
            'formAdress' => $form->createView()
        ]);
    }
}
