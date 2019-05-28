<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtRefreshTokenAuthenticator extends AbstractGuardAuthenticator
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
        return $request->request->get('grant_type') === 'refresh_token' && strtolower($request->getMethod()) === 'post' && preg_match("/\/auth\/login$/", $request->getUri());

    }

    public function getCredentials(Request $request)
    {
        $payload = $request->request->get('refresh_token');

        try {
            $token = $this->jwtService->decode($payload);

        } catch (\Exception $ex) {
            throw new AuthenticationException("Invalid refresh token");
        }

        if (!isset($token->type) || $token->type !== 'refresh') {
            throw new AuthenticationException("Invalid refresh token");
        }

        return [
            'username' => $token->name
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
        return new JsonResponse(['message' => $exception->getMessage()], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $accessToken = $this->jwtService->createAccessToken($token);
        $refreshToken = $this->jwtService->createRefreshToken($token);
        return new JsonResponse(['access_token' => $accessToken, 'refresh_token' => $refreshToken, 'expires_in' => $this->container->get('jwt_token_ttl')], 200);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
