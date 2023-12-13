<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use App\Controller;


class InscriController extends AbstractController
{
    /**
     * @Route("/inscri/", name="inscri_accueil")
     */
    public function index(Request $request,AbonneRepository $repo): Response
    {
        $form = $this -> createFormBuilder ( )
            -> add ( 'critere' , TextType :: class,["required"=>false] )
            -> add ( 'chercher' , SubmitType :: class , [ 'label' => 'Chercher' ])
            ->getForm ();
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {   
            $data=$form->getData();
            if ($data == null){
                $lesAbonnes=$repo->findAll();
            }
            else $lesAbonnes=$repo->recherche($data);
            
        }
        else
        {
            $lesAbonnes=$repo->findAll();
        }
            return $this -> render ( 'inscri/index.html.twig',['lesAbonnes'=>$lesAbonnes,'f'=>$form->createView()] );
    }

    /**
* @Route("/inscri/voir/{id}", name="inscri_voir")
*/
public function voir(Abonne $abonne)
{
    return $this->render('inscri/voir.html.twig', ["abonne"=>$abonne ]);
}

/**
* @Route("/inscri/ajouter/", name="inscri_ajouter")
*/
public function ajouter(Request $request)
{
$abonne = new Abonne();
$form =$this->createForm(AbonneType::class,$abonne);
$form->add('valider',SubmitType::class);    
$form->handleRequest($request);
if($form->isSubmitted()) {
$em=$this->getDoctrine()->getManager();
$em->persist($abonne);
$em->flush();
$session = new Session();
$session->getFlashBag()->add('notice', 'Abonnée bien ajouter !');
return $this->redirectToRoute('inscri_accueil');
}
return $this->render('inscri/ajouter.html.twig', ['f' => $form->createView()]);
}

     /**
* @Route("/inscri/supprimer/{id}", name="inscri_supprimer")
*/
public function supprimer($id)
{
    $em = $this->getDoctrine()->getManager();
    $item = $em->getRepository(Abonne::class)->find($id);
    $em->remove($item);
    $em->flush();
    $session = new Session();
    $session->getFlashBag()->add('notice', "L'abonnée $id a été supprimer");
    return $this->redirectToRoute('inscri_accueil');
}

/**
* @Route("/inscri/modifier/{id}", name="inscri_modifier")
*/
public function modifier($id,Request $request)
{
    $em = $this->getDoctrine()->getManager();
    $repo=$em->getRepository(Abonne::class);
    $abonne=$repo-> find($id);
    $form =$this->createForm(AbonneType::class,$abonne);
    $form->add('valider',SubmitType::class);    
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()) {
$em=$this->getDoctrine()->getManager();
$em->persist($abonne);
$em->flush();
$session = new Session();
$session->getFlashBag()->add('notice', 'Abonnée mis à jour !');
return $this->redirectToRoute('inscri_accueil');
}
return $this->render('inscri/modifier.html.twig', ['f' => $form->createView(),"abonne"=>$abonne ]);
    
}
}