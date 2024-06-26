<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserEvent;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Security;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EventDispatcherInterface $eventDispatcher;


    public function __construct(EmailVerifier $emailVerifier, EventDispatcherInterface $eventDispatcher)
    {
        $this->emailVerifier = $emailVerifier;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,EventDispatcherInterface $eventDispatcher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();


            $event = new UserEvent($user);
            $eventDispatcher->dispatch($event, UserEvent::CONFIRMATION);

            $this->addFlash('success', 'Un email de confirmation a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception.');

            // generate a signed url and email it to the user
            //$this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //    (new TemplatedEmail())
            //        ->from(new Address('exemple.mailer@jennifer.com', 'Jennifer'))
            //        ->to($user->getEmail())
            //        ->subject('Please Confirm your Email')
            //        ->htmlTemplate('registration/confirmation_email.html.twig')
            //);
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, Security $security): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre e-mail a été vérifier !');

        return $this->redirectToRoute('admin.formations');
    }
}
