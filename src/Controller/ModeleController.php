<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EntityType;;

#[Route('/modele')]
class ModeleController extends AbstractController
{
    #[Route('/', name: 'app_modele')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $modele = new Modele();
        $form = $this->createForm(ModeleType::class, $modele);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($modele);
            $em->flush();

            $this->addFlash('success','Modéle ajouté');

        };

        $modeles = $em->getRepository(Modele::class)->findAll();
        return $this->render('modele/index.html.twig', [
            'modeles' => $modeles,
            'ajout'=> $form->createView(),
        ]);
    }
    
    #[Route('/detail/{id}', name: 'modele')]
    public function modele (
    Modele $modele = null, 
    Request $request, 
    EntityManagerInterface $em
    ): Response {

        if($modele == null) {
            $this->addFlash('error','Modéle introuvable');

        return $this-> redirectToRoute('app_modele');
        }

        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($modele);
            $em->flush();

            $this->addFlash('success','Modéle mis à jour');
        }
        return $this->render('modele/show.html.twig', [
            'modele'=> $modele,
            'edit'=> $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name:'delete_modele')]
    public function delete (Request $request, EntityManagerInterface $em, Modele $modele = null
    ): Response {
        if($modele == null) {
            $this->addFlash('danger','Modéle introuvable');
            return $this->redirectToRoute('app_modele');
        }
        $em->remove($modele);
        $em->flush();
        
        $this->addFlash('success','Modéle supprimé');
        return $this->redirectToRoute('app_modele');
    }
    
    #[Route('/ajout', name:'ajout_modele')]
    public function ajout (
        EntityManagerInterface $em,
        Request $request,   
        ): Response {
            $modele = new Modele();
            $form = $this->createForm(ModeleType::class, $modele);
    
            $form-> handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($modele);
                $em->flush();
            
                $this->addFlash('success','Modéle ajoutée');
            };

             return $this->render('modele/show.html.twig', [
                'edit'=> $form->createView()
            ]);
        }
}