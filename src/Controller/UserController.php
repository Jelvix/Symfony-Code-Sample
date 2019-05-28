<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


class UserController extends AbstractController
{

    /**
     * Create new system user
     * @Post("/api/system/users", name = "app.system_users_create")
     * @SWG\Parameter(
     *     name="User data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Create new user",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function addUser(Request $request)
    {
        $user = null;
        try {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        }catch (\Exception $ex) {
            throw new BadRequestHttpException('Invalid user data is given');
        }

        try {
            $this->em->getRepository(User::class)->save($user);
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($user, JsonResponse::HTTP_CREATED, [], ['groups' => 'full']);
    }

    /**
     * Update system user
     * @Put("/api/system/users/{userId}", name = "app.system_users_update")
     * @SWG\Parameter(
     *     name="User data",
     *     in="body",
     *     type="object",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"full"})
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Update user",
     *     @SWG\Schema(
     *         type="object",
     *         ref=@Model(type=User::class, groups={"full"})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     * @param int $userId
     * @param Request $request
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function updateUser($userId, Request $request)
    {
        $user = null;
        try {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        }catch (\Exception $ex) {
            throw new BadRequestHttpException('Invalid user data is given');
        }

        try {
            $user = $this->em->getRepository(User::class)->save($user, $userId);
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'full']);
    }

    /**
     * Delete user
     * @Delete("/api/system/users/{userId}", name = "app.system_users_delete")
     * @SWG\Response(
     *     response=200,
     *     description="Delete user",
     *     @SWG\Schema(
     *         type="object",
     *         properties={@SWG\Property(type="string", property="message")})
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     * @param int $userId
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @return Response
     */
    public function deleteUser($userId)
    {
        try {
            $this->em->getRepository(User::class)->delete($userId);
        }catch (\Exception $ex){
            throw new UnprocessableEntityHttpException($ex->getMessage());
        }

        return $this->json(['message' => 'ok'], JsonResponse::HTTP_OK);
    }

    /**
     * Get users list for webpanel
     * @Get("/api/system/users", name = "app.system_users_list")
     * @SWG\Response(
     *     response=200,
     *     description="Returns users list",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function getUsers()
    {
        return $this->json( $this->em->getRepository(User::class)->findAll(), 200, [], ['groups' => 'full']);
    }


}
