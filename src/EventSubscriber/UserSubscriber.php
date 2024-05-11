<?php

namespace App\EventSubscriber;

use App\Event\UserEvent;
use App\Security\EmailVerifier;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;




class UserSubscriber implements EventSubscriberInterface


{

    private $noReplyEmail;
    private $mailer;
    private $emailVerifier;
    private $router;




    public function __construct( $noReplyEmail, MailerInterface $mailer, EmailVerifier $emailVerifier, RouterInterface $router){

       $this->noReplyEmail = $noReplyEmail;
       $this->mailer = $mailer;
       $this->emailVerifier = $emailVerifier;
       $this->router = $router;

    }

    public function onConfirmationEmail(UserEvent $event): void
    {

        $user = $event->getUser();
        $router = $this->router;



        /*$email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($user->getEmail())
            ->subject('Welcome')
            ->text('Merci pour votre inscription !')
            ->htmlTemplate('emails/signup.html.twig')*/


            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,(new TemplatedEmail())
                    ->from(new Address('exemple.mailer@jennifer.com', 'Jennifer'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'user' => $user,
                                        ])
                                        );
                                    
                                    }
        

           /* ->context([
                'user' => $user,
            ]);*/
        
           //this->mailer->send($email);
    

    public static function getSubscribedEvents(): array

    {
        return [
            UserEvent::CONFIRMATION => 'onConfirmationEmail',
        ];
    }
}
