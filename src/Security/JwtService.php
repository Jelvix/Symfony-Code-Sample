<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 20.01.2019
 * Time: 12:18
 */

namespace App\Security;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class JwtService implements OAth2TokenServiceInterface
{

    /**
     * @var ParameterBagInterface
     */
    private $container;

    public function __construct(ParameterBagInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param TokenInterface $token
     * @return string
     */
    public function createAccessToken(TokenInterface $token)
    {
        $privateKey = file_get_contents($this->container->get('jwt_private_key_path'));
        // var_export($privateKey);
        $accessToken = JWT::encode([
            "sub" => $token->getUser()->getId(),
            "username" => $token->getUser()->getUsername(),
            "name" => $token->getUser()->getDisplayName(),
            "avatar" => $token->getUser()->getAvatar(),
            "roles" => $token->getUser()->getRoles(),
            "iss" => $this->container->get('router.request_context.host'),
            "exp" => time() + $this->container->get('jwt_token_ttl')
        ], $privateKey, 'RS256');
        return $accessToken;
    }

    /**
     * @param TokenInterface $token
     * @return string
     */
    public function createRefreshToken(TokenInterface $token)
    {
        $privateKey = file_get_contents($this->container->get('jwt_private_key_path'));
        // var_export($privateKey);
        $accessToken = JWT::encode([
            "sub" => $token->getUser()->getId(),
            "username" => $token->getUser()->getUsername(),
            "name" => $token->getUser()->getDisplayName(),
            "avatar" => $token->getUser()->getAvatar(),
            "type" => 'refresh',
            "iss" => $this->container->get('router.request_context.host'),
            "exp" => time() + ($this->container->get('jwt_refresh_token_ttl'))
        ], $privateKey, 'RS256');
        return $accessToken;
    }

    /**
     * @param $tokenPayload
     * @return object
     * @throws SignatureInvalidException
     * @throws ExpiredException
     * @throws BeforeValidException
     */
    public function decode($tokenPayload)
    {
        $key = file_get_contents($this->container->get('jwt_public_key_path'));
        return JWT::decode($tokenPayload, $key, ['RS256']);
    }
}