<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var ParameterBagInterface
     */
    private $container;

    /**
     * @var OAth2TokenServiceInterface
     */
    private $jwtService;

    public function __construct(ParameterBagInterface $container, OAth2TokenServiceInterface $jwtService)
    {
        $this->container = $container;
        $this->jwtService = $jwtService;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') && preg_match("/^Bearer\s+.*/", $request->headers->get('Authorization'));

    }

    public function getCredentials(Request $request)
    {
        $payload = preg_replace("/^Bearer\s+(.*)$/i", "$1", $request->headers->get('Authorization'));

        try {
            $token = $this->jwtService->decode($payload);

        } catch (\Exception $ex) {
            throw new AuthenticationException("Invalid token");
        }

        return [
            'username' => $token->username
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);
        if (!$user) {
            throw new AuthenticationException("Invalid username");
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message' => 'Invalid token'], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse('Authorization header required', 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
