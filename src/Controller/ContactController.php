<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{

    #[Route('/', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $email = (new Email())
            ->from('hello@example.com')
                ->to('you@example.com')
                ->subject('¡Bienvenido!')
                ->html('<p>'.$form->getData()['name'].'</p>'.
                        '<p>'.$form->getData()['email'].'</p>'.
                        '<p>'.$form->getData()['texto'].'</p>'                
                    );

            $mailer->send($email);
            $this->addFlash('success', '¡El correo se envío correctamente.!');
        }

        return $this->render('index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
