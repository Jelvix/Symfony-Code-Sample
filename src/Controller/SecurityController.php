<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 22.01.2019
 * Time: 14:33
 */

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

class SecurityController extends AbstractController
{

    /**
     * Login user by username and password of.     *
     *
     * @Post("/api/auth/login", name = "app.auth_login")
     * @SWG\Response(
     *     response=200,
     *     description="Returns token info",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *             @SWG\Property(property="access_token", type="string"),
     *             @SWG\Property(property="refresh_token", type="string"),
     *             @SWG\Property(property="expires_in", type="integer")
     *         }
     *     )
     * )
     * @SWG\Parameter(
     *     name="Auth data",
     *     description="Authentication data like email/password with grant_type == 'password' or refresh_token with grant_type == 'refresh_token'",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *        properties={
     *          @SWG\Property(property="username", type="string"),
     *          @SWG\Property(property="password", type="string"),
     *          @SWG\Property(property="refresh_token", type="string", description="if 'grant_type' == 'refresh_token'"),
     *          @SWG\Property(property="grant_type", type="string")
     *       },
     *       required={"email", "password"}
     *     )
     * )
     *
     * @SWG\Tag(name="authorization")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function login()
    {
        return $this->json([], 200);
    }

    /**
     * Load acl permissions list for webpanel
     * @Get("/api/auth/acl", name = "app.auth_acl")
     * @SWG\Response(
     *     response=200,
     *     description="Returns permissions list",
     *     @SWG\Schema(
     *         type="object",
     *         properties={
     *            @SWG\Property(
     *                property="roles",
     *                type="array",
     *                @SWG\Items(ref=@Model(type=UserRole::class, groups={"short"}))
     *           ),
     *           @SWG\Property(
     *                property="permissions",
     *                type="array",
     *                @SWG\Items(ref=@Model(type=Permission::class, groups={"short"}))
     *           )
     *        }
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @SWG\Schema(
     *         type="object",
     *         properties={@SWG\Property(type="string", property="message")})
     *     )
     * )
     * @SWG\Tag(name="authorization")
     * @Security(name="Bearer")
     */
    public function loadWebpanelPermissions()
    {
        return $this->json([
            'roles' => $this->em->getRepository(UserRole::class)->findAll(),
            'permissions' => $this->em->getRepository(Permission::class)->findBy(['type' => 'web'])
        ], 200, [], ['groups' => 'short']);
    }

}