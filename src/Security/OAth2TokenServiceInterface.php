<?php
/**
 * Created by PhpStorm.
 * User: a.itsekson
 * Date: 30.01.2019
 * Time: 22:53
 */

namespace App\Security;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface OAth2TokenServiceInterface
{
    /**
     * @param TokenInterface $token
     * @return mixed
     */
    public function createAccessToken(TokenInterface $token);

    /**
     * @param TokenInterface $token
     * @return mixed
     */
    public function createRefreshToken(TokenInterface $token);

    /**
     * @param $tokenPayload
     * @return object
     */
    public function decode($tokenPayload);
}