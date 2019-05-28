<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 23:32
 */

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

class UsernameAndPasswordAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var ParameterBagInterface
     */
    private $container;
    /**
     * @var OAth2TokenServiceInterface
     */
    private $jwtService;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $container, OAth2TokenServiceInterface $jwtService)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->container = $container;
        $this->jwtService = $jwtService;
    }

    public function supports(Request $request)
    {
        return (!$request->request->get('grant_type') || $request->request->get('grant_type') === 'password') &&
            strtolower($request->getMethod()) === 'post' &&
            preg_match("/\/auth\/login$/", $request->getUri());
    }

    public function getCredentials(Request $request)
    {
        $name = $request->request->get('username');
        $password = $request->request->get('password');
        return [
            'username' => $name,
            'password' => $password,
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
        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            return true;
        }
        throw new AuthenticationException("Invalid credentials");
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message' => 'Invalid credentials'], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $accessToken = $this->jwtService->createAccessToken($token);
        $refreshToken = $this->jwtService->createRefreshToken($token);
        return new JsonResponse(['access_token' => $accessToken, 'refresh_token' => $refreshToken, 'expires_in' => $this->container->get('jwt_token_ttl')], 200);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(['message' => 'Unauthorized'], 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
