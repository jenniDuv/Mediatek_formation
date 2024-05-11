<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
class KeycloakController extends AbstractController
{
/**
* Link to this controller to start the "connect" process
*
* @Route("/connect/keycloak", name="connect_keycloak_start")
*/
public function connectAction(ClientRegistry $clientRegistry)
{
// will redirect to Keycloack!
return $clientRegistry
->getClient('keycloak') // key used in config/packages/knpu_oauth2_client.yaml
->redirect([
            'openid',
            'email' // the scopes you want to access
], []);
}
/**
* After going to Facebook, you're redirected back here
* because this is the "redirect_route" you configured
* in config/packages/knpu_oauth2_client.yaml
*
* @Route("/connect/keycloak/check", name="connect_keycloak_check")
*/
public function connectCheckAction(Request $request, ClientRegistry $clientRegistry, UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher)
{
// ** if you want to *authenticate* the user, then
// leave this method blank and create a Guard authenticator
// (read below)
/** @var \KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient $client */
$client = $clientRegistry->getClient('keycloak');
dump($client);
try {
// the exact class depends on which provider you're using
/** @var \Stevenmaguire\OAuth2\Client\Provider\Keycloak $user */
//dump($client);
//$accessToken = $client->getAccessToken();
//dump($accessToken);
// If access token is successfully fetched, you can use it to make authenticated requests
$userClient = $client->fetchUser();
// do something with all this new power!
// e.g. $name = $user->getFirstName();
dump($userClient);
$user = $userRepository->findOneBy(["email" => $userClient->getEmail()]);
if (!$user) {
$user = new User();
$user->setEmail($userClient->getEmail())
->setPassword($userPasswordHasher->hashPassword($user, "12345"));
$manager->persist($user);
$manager->flush();
}

$userAuthenticator->authenticateUser($user, $formLoginAuthenticator, $request);
return $this->redirectToRoute("accueil");
// ...
} catch (IdentityProviderException $e) {
// something went wrong!
// probably you should return the reason to the user
dump($e);
dump($e->getMessage());
} catch (\Exception $e) {
dump($e);
dump($e->getMessage());
$this->addFlash("danger", "Réessayer encore !");
return $this->redirectToRoute("accueil");
}
return $this->redirectToRoute("accueil");


}

}