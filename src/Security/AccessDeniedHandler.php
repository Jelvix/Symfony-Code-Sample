<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 20.01.2019
 * Time: 13:23
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new JsonResponse([
            "message" => 'Access Denied'
        ], 403);
    }
}