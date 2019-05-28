<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 23.01.2019
 * Time: 23:23
 */

namespace App\Controller;


use App\Entity\Permission;
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

class PermissionsController extends AbstractController
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
     * Create new permission
     * @Post("/api/permissions", name = "app.permissions_create")
     * @SWG\Parameter(
     *     name="Permission data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Permission::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Create new permission",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Permission::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="permissions")
     * @Security(name="Bearer")
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function addPermission(Request $request)
    {
        $permission = null;
        try {
            $permission = $this->serializer->deserialize($request->getContent(), Permission::class, 'json');
        }catch (\Exception $ex) {
            throw new BadRequestHttpException('Invalid permission data is given');
        }

        try {
            $this->em->getRepository(Permission::class)->save($permission);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($permission, JsonResponse::HTTP_CREATED);
    }

    /**
     * Update permission
     * @Put("/api/permissions/{permissionId}", name = "app.permissions_update")
     * @SWG\Parameter(
     *     name="Permission data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Permission::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Update permission",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=Permission::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="permissions")
     * @Security(name="Bearer")
     * @param int $permissionId
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function updatePermission($permissionId, Request $request)
    {
        $permission = null;
        try {
            $permission = $this->serializer->deserialize($request->getContent(), Permission::class, 'json');
        }catch (\Exception $ex) {
            throw new BadRequestHttpException('Invalid permission data is given');
        }

        try {
            $permission = $this->em->getRepository(Permission::class)->save($permission, $permissionId);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($permission, JsonResponse::HTTP_OK);
    }

    /**
     * Delete permission
     * @Delete("/api/permissions/{permissionId}", name = "app.permissions_delete")
     * @SWG\Response(
     *     response=200,
     *     description="Delete permission",
     *     @SWG\Schema(
     *         type="object",
     *         properties={@SWG\Property(type="string", property="message")})
     *     )
     * )
     * @SWG\Tag(name="permissions")
     * @Security(name="Bearer")
     * @param int $permissionId
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function deletePermission($permissionId)
    {
        try {
            $this->em->getRepository(Permission::class)->delete($permissionId);
            if($this->permissionsProvider instanceof ReloadCacheInterface) {
                $this->permissionsProvider->reloadCache();
            }
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json(['message' => 'ok'], JsonResponse::HTTP_OK);
    }

    /**
     * Get permissions list for webpanel
     * @Get("/api/permissions", name = "app.permissions_list")
     * @SWG\Response(
     *     response=200,
     *     description="Returns permissions list",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Permission::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="permissions")
     * @Security(name="Bearer")
     */
    public function getPermissions()
    {
        return $this->json( $this->em->getRepository(Permission::class)->findAll(), 200, [], ['groups' => 'full']);
    }
}