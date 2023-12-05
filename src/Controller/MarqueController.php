<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/marque')]
class MarqueController extends AbstractController
{
    #[Route('/', name: 'app_marque')]
    public function index( EntityManagerInterface $em, Request $request, ): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($marque);
            $em->flush();

            $this->addFlash('success','Produit ajouté');
        };

        $marques = $em->getRepository(Marque::class)->findAll();
            
            return $this->render('marque/index.html.twig', [
            'marques' => $marques,
            'ajout' => $form->createView()
        ]);
    }
    #[Route('/detail/{id}', name:'marke')]
    public function marke ( 
        Marque $marque = null, 
        EntityManagerInterface $em, 
        Request $request,
        ): Response {
        if ($marque == null) {
            $this->addFlash('error','Marque Introuvable');
            return $this->redirectToRoute('app_marque');
        } 
            $form = $this->createForm(MarqueType::class, $marque);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($marque);
                $em->flush();

                $this->addFlash('success','Marque ajouté');
            }   
            return $this->render('marque/show.html.twig' , [
                'marq'=> $marque,
                'edit'=> $form->createView()
            ]);
        }


        #[Route('/delete/{id}', name:'delete_marque')]
        public function delete (Marque $marque = null, EntityManagerInterface $em,
        ) : Response{
            if ($marque == null) { 
                $this->addFlash('error','Marque Introuvable');
                return $this->redirectToRoute('app_marque');
            }
            $em->remove($marque);
            $em->flush();

            $this->addFlash('success','Marque supprimé');
            return $this->redirectToRoute('app_marque',);

        }
        
        #[Route('/ajout', name:'ajout_marque')]
        public function ajout (
            Request $request, 
            EntityManagerInterface $em,
        ): Response {
            $marque = new Marque();
            $form = $this->createForm(MarqueType::class, $marque);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $logoFile = $form->get('logo')->getData();

                if ($logoFile) {
                    $newFilename = uniqid().'.'.$logoFile->guessExtension();
    
                    try {
                        $logoFile->move(
                            $this->getParameter('upload_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this-> addFlash('error', $e->getMessage());
                    }

                    $marque->setLogo($newFilename);
                }
                $em->persist($marque);
                $em->flush();

                $this->addFlash('success','Marque ajouté');
                return $this->redirectToRoute('app_marque');
    };
    
    return $this->render('marque/ajout.html.twig', [
        'ajout'=> $form->createView()
        ]);
    }
}
