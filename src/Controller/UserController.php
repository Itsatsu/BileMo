<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{

    /**
     * @OA\Response(
     *     response=200,
     *     description="Retourne les details d'un utilisateur",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class, groups={"getUserDetail"}))
     *    )
     * )
     * @OA\Response(
     *       response=401,
     *       description="Non authentifié",
     *   )
     * @OA\Response(
     *     response=403,
     *     description="Accès refusé",
     *     )
     * @OA\Response(
     *       response=404,
     *       description="Resource non trouvée",
     *   )
     * @OA\Response(
     *      response=500,
     *      description="Erreur serveur",
     *      )
     *
     * @OA\Tag(name="Users")
     *
     */
    #[Route('api/users/{id}', name: 'app_user_detail', methods: ['GET'])]
    public function getUserDetail(User $user, SerializerInterface $serializer): JsonResponse
    {
        if ($user->getCustomer() !== $this->getUser()) {
            return new JsonResponse(
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        $context = SerializationContext::create()->setGroups(['getUserDetail']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @OA\Response(
     *     response=204,
     *     description="Supprime un utilisateur",
     * )
     * @OA\Response(
     *        response=401,
     *        description="Non authentifié",
     *    )
     * @OA\Response(
     *      response=403,
     *      description="Accès refusé",
     *      )
     * @OA\Response(
     *        response=404,
     *        description="Resource non trouvée",
     *    )
     * @OA\Response(
     *      response=500,
     *      description="Erreur serveur",
     *      )
     * @OA\Tag(name="Users")
     *
     */
    #[Route('api/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cache): JsonResponse
    {
        if ($user->getCustomer() !== $this->getUser()) {
            return new JsonResponse(
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        $cache->invalidateTags(['usersCache' . $user->getCustomer()->getId()]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );

    }

    /**
     * @throws InvalidArgumentException
     *
     * @OA\Response(
     *     response=204,
     *     description="Met à jour un utilisateur",
     * )
     * @OA\Response(
     *        response=401,
     *        description="Non authentifié",
     *    )
     * @OA\Response(
     *      response=403,
     *      description="Accès refusé",
     *      )
     * @OA\Response(
     *        response=404,
     *        description="Resource non trouvée",
     *    )
     * @OA\Response(
     *      response=500,
     *      description="Erreur serveur",
     *      )
     * @OA\Tag(name="Users")
     * @OA\RequestBody(
     *      @OA\JsonContent(
     *           example={
     *              "email": "email@test.fr",
     *              "firstname": "firstname",
     *              "lastname": "lastname",
     *           },
     *      )
     * )
     */
    #[Route('api/users/{id}', name: 'user_update', methods: ['PUT'])]
    public function updateUser(
        User                        $currentUser,
        SerializerInterface         $serializer,
        EntityManagerInterface      $em,
        Request                     $request,
        ValidatorInterface          $validator,
        TagAwareCacheInterface      $cache,
    ): JsonResponse {
        if ($currentUser->getCustomer() !== $this->getUser()) {
            return new JsonResponse(
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json');
        //on met a jour uniquement les champs modifiés

        if ($updatedUser->getEmail() !== null) {
            $currentUser->setEmail($updatedUser->getEmail());
        }

        if ($updatedUser->getFirstname() !== null) {
            $currentUser->setFirstname($updatedUser->getFirstname());
        }

        if ($updatedUser->getLastname() !== null) {
            $currentUser->setLastname($updatedUser->getLastname());
        }

        $errors = $validator->validate($currentUser);
        if (count($errors) > 0) {
            $jsonErrors = $serializer->serialize($errors, 'json');
            return new JsonResponse(
                $jsonErrors,
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        //On récupère l'id du customer avant la modification et on invalide le cache
        $customer = $currentUser->getCustomer()->getId() ?? null;
        if ($customer !== null) {
            $cache->invalidateTags(['usersCache' . $currentUser->getCustomer()->getId()]);
        }

        $em->persist($currentUser);
        $em->flush();

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );

    }

    /**
     * @throws InvalidArgumentException
     * @OA\Response(
     *     response=201,
     *     description="Crée un utilisateur",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class , groups={"getUserDetail"})))
     *   )
     * )
     * @OA\Tag(name="Users")
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *          example={
     *              "email": "email@test.fr",
     *              "firstname": "firstname",
     *              "lastname": "lastname",
     *          },
     * )
     * )
     * @OA\Response(
     *        response=401,
     *        description="Non authentifié",
     *    )
     * @OA\Response(
     *      response=403,
     *      description="Accès refusé",
     *      )
     * @OA\Response(
     *        response=404,
     *        description="Resource non trouvée",
     *    )
     * @OA\Response(
     *      response=500,
     *      description="Erreur serveur",
     *      )
     */
    #[Route('api/users', name: 'user_create', methods: ['POST'])]
    public function createUser(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $em,
        ValidatorInterface     $validator,
        UrlGeneratorInterface  $urlGenerator,
        TagAwareCacheInterface $cache
    ): JsonResponse {

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $jsonErrors = $serializer->serialize($errors, 'json');
            return new JsonResponse(
                $jsonErrors,
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $user->setCustomer($this->getUser());
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();
        $cache->invalidateTags(['usersCache' . $user->getCustomer()->getId()]);
        $location = $urlGenerator->generate(
            'app_user_detail',
            ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = SerializationContext::create()->setGroups(['getUserDetail']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_CREATED,
            ['Location' => $location],
            true
        );
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs d'un customer",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class, groups={"getUserDetail"})))
     *  )
     * )
     * @OA\Parameter(
     *      name="page",
     *      in="query",
     *      description="Page que vous souhaitez récupérer",
     *      @OA\Schema(type="int")
     *  )
     * @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      description="Nombre d'éléments par page que vous souhaitez récupérer",
     *      @OA\Schema(type="int")
     *  )
     * @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *     )
     * @OA\Response(
     *       response=403,
     *       description="Accès refusé",
     *       )
     * @OA\Response(
     *         response=404,
     *         description="Resource non trouvée",
     *     )
     * @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     )
     *
     * @OA\Tag(name="Customers")
     */
    #[Route('api/customers/{id}/users', name: 'customer_users', methods: ['GET'])]
    public function getCustomerUsers(Customer $customer, UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {

        if ($customer !== $this->getUser()) {
            return new JsonResponse(
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        $page = $request->query->get('page', 1);

        $limit = $request->query->get('limit', 3);
        if ($limit > 10) {
            $limit = 10;
        }
        $idCache = 'customer-users-list-' . $customer->getId() . '-' . $page . '-' . $limit;

        $jsonUsers = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $customer, $page, $limit, $serializer) {
            $item->tag('usersCache' . $customer->getId());
            $users = $userRepository->findByCustomerUserPagined($customer->getId(), $page, $limit);
            $context = SerializationContext::create()->setGroups(['getUserDetail']);
            return $serializer->serialize($users, 'json', $context);
        });

        return new JsonResponse(
            $jsonUsers,
            Response::HTTP_OK,
            [],
            true
        );

    }
}
