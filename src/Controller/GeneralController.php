<?php

namespace App\Controller;

use App\Entity\CategorieService;
use App\Form\ContactType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    /**
     * @Route("/general", name="general")
     */
    public function index(): Response
    {
        return $this->render('general/index.html.twig', [
            'controller_name' => 'GeneralController',
        ]);
    }
    /**
     * @Route("/",name="accueil")
     */
    public function accueil( SessionInterface $session,ServiceRepository $repo){
        $categories=$this->getDoctrine()->getRepository(CategorieService::class)->findAll();
        $services=$repo->findAll();
        $session->set('categories',$categories);
        $session->set('services',$services);
        return $this->render('general/accueil.html.twig');
    }
    /**
     * @Route("/about",name="about")
     */
    public function about(ServiceRepository $repo){
        $services=$repo->serviceforabout();
        return $this->render('general/about.html.twig',['services'=>$services]);
    }
    /**
     * @Route("/contact",name="contact")
     */
    public function contact(Request $request,\Swift_Mailer $mailer){
        $form=$this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $contact=$form->getData();
            $message=(new \Swift_Message('nouveau contact'))
                ->setFrom('docdocpidev@gmail.com')
                ->setTo('khalil.bentili@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        compact('contact')),
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash('message','le message a bien été envoyé');
            return $this->redirectToRoute('accueil');
        }
        return $this->render('general/contact.html.twig',['f'=>$form->createView()]);
    }
}
