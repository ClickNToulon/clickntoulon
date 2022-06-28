<?php

namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Stevenmaguire\OAuth2\Client\Provider\MicrosoftResourceOwner;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\{Badge\UserBadge, SelfValidatingPassport};
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class MicrosoftAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected string $serviceName = "microsoft";

    public const LOGIN_ROUTE = "app_login";

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return "oauth_check" === $request->attributes->get("_route") &&
            $request->get("service") === $this->serviceName;
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->clientRegistry->getClient($this->serviceName);
        $accessToken = $this->fetchAccessToken($client);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use (
                $accessToken,
                $client
            ) {
                /** @var MicrosoftResourceOwner $microsoftUser */
                $microsoftUser = $client->fetchUserFromToken($accessToken);
                return $this->userRepository->findorCreateFromMicrosoftOauth(
                    $microsoftUser
                );
            })
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): RedirectResponse {
        $request->request->set("_remember_me", "1");

        return new RedirectResponse($this->urlGenerator->generate("home"));
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): RedirectResponse {
        if ($request->hasSession()) {
            $request
                ->getSession()
                ->set(Security::AUTHENTICATION_ERROR, $exception);
        }
        return new RedirectResponse(
            $this->urlGenerator->generate(self::LOGIN_ROUTE)
        );
    }
}