<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 25.01.2019
 * Time: 23:32
 */

namespace App\Controller;


use App\Entity\UserRole;
use App\Security\PermissionsProviderInterface;
use App\Utils\ReloadCacheInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

class UserRolesController extends AbstractController
{
    /**
     * @var PermissionsProviderInterface
     */
    private $permissionsProvider;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, PermissionsProviderInterface $permissionsProvider)
    {
        parent::__construct($em, $serializer);
        $this->permissionsProvider = $permissionsProvider;
    }
    /**
     * Get user roles list for webpanel
     * @Get("/api/user-roles/{group}", name = "app.user_roles_list")
     * @SWG\Response(
     *     response=200,
     *     description="Returns user roles list",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=UserRole::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="group",
     *     description="group like 'full' or 'short'",
     *     type="string",
     *     in="path"
     * )
     * @SWG\Tag(name="user roles")
     * @Security(name="Bearer")
     * @param string $group
     * @return Response
     */
    public function getUserRoles($group = 'full')
    {
        return $this->json( $this->em->getRepository(UserRole::class)->findAll(), 200, [], ['groups' => $group]);
    }

    /**
     * Create new user role
     * @Post("/api/user-roles", name = "app.user_roles_create")
     * @SWG\Parameter(
     *     name="User role data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=UserRole::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Create new user role",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=UserRole::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="user roles")
     * @Security(name="Bearer")
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function addRole(Request $request)
    {
        $role = null;
//        try {
            $role = $this->serializer->deserialize($request->getContent(), UserRole::class, 'json');
//        }catch (\Exception $ex) {
//            throw new BadRequestHttpException('Invalid user role data is given');
//        }

        try {
            $this->em->getRepository(UserRole::class)->save($role);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($role, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update user role
     * @Put("/api/user-roles/{roleId}", name = "app.user_roles_update")
     * @SWG\Parameter(
     *     name="User role data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=UserRole::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Update user role",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=UserRole::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="user roles")
     * @Security(name="Bearer")
     * @param int $roleId
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function updateRole($roleId, Request $request)
    {
        $role = null;
        try {
            $role = $this->serializer->deserialize($request->getContent(), UserRole::class, 'json');
        }catch (\Exception $ex) {
            throw new BadRequestHttpException('Invalid role data is given');
        }

        try {
            $role = $this->em->getRepository(UserRole::class)->save($role, $roleId);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($role, JsonResponse::HTTP_OK);
    }

    /**
     * Delete user role
     * @Delete("/api/user-roles/{roleId}", name = "app.user_roles_delete")
     * @SWG\Response(
     *     response=200,
     *     description="Delete user role",
     *     @SWG\Schema(
     *         type="object",
     *         properties={@SWG\Property(type="string", property="message")})
     *     )
     * )
     * @SWG\Tag(name="user roles")
     * @Security(name="Bearer")
     * @param int $roleId
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function deleteRole($roleId)
    {
        try {
            $this->em->getRepository(UserRole::class)->delete($roleId);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json(['message' => 'ok'], JsonResponse::HTTP_OK);
    }


}