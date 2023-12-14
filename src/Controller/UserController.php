<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{

    #[Route('api/users/{id}', name: 'app_user_detail', methods: ['GET'])]
    public function getUserDetail(User $user, SerializerInterface $serializer): JsonResponse
    {
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUserDetail']);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('api/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(['usersCache' . $user->getCustomer()->getId()]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );

    }

    #[Route('api/users/{id}', name: 'user_update', methods: ['PUT'])]
    public function updateUser(User $user, SerializerInterface $serializer, EntityManagerInterface $em, Request $request, CustomerRepository $customerRepository, ValidatorInterface $validator, TagAwareCacheInterface $cache, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        $errors = $validator->validate($updatedUser);
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
        $customer = $user->getCustomer()->getId() ?? null;
        if ($customer !== null) {
            $cache->invalidateTags(['usersCache' . $user->getCustomer()->getId()]);
        }

        $content = $request->toArray();
        $idCustomer = $content['customerId'] ?? null;
        $password = $content['password'] ?? null;

        //mot de passe modifié
        if ($password !== null) {
            $updatedUser->setPassword(
                $passwordHasher->hashPassword(
                    $updatedUser,
                    $content['password']
                )
            );
        }

        //Customer modifié
        if ($idCustomer !== null) {
            $newCustomer = $customerRepository->find($idCustomer);
            $updatedUser->setCustomer($newCustomer);
        }

        $cache->invalidateTags(['usersCache' . $updatedUser->getCustomer()->getId()]);

        $em->persist($updatedUser);
        $em->flush();

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );

    }

    #[Route('api/users', name: 'user_create', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, CustomerRepository $customerRepository, ValidatorInterface $validator, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {

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
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );
        $content = $request->toArray();
        $idCustomer = $content['customerId'] ?? null;
        $user->setCustomer($customerRepository->find($idCustomer));
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();
        $cache->invalidateTags(['usersCache' . $user->getCustomer()->getId()]);
        $location = $urlGenerator->generate('app_user_detail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getUserDetail']);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_CREATED,
            ['Location' => $location],
            true
        );
    }

    #[Route('api/customers/{id}/users', name: 'customer_users', methods: ['GET'])]
    public function getCustomerUsers(Customer $customer, UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 3);

        $idCache = 'customer-users-list-' . $customer->getId() . '-' . $page . '-' . $limit;

        $jsonUsers = $cache->get($idCache, function (ItemInterface $item) use ($userRepository, $customer, $page, $limit, $serializer) {
            $item->tag('usersCache' . $customer->getId());
            $users = $userRepository->findByCustomerUserPagined($customer->getId(), $page, $limit);
            return $serializer->serialize($users, 'json', ['groups' => 'getUserDetail']);
        });

        return new JsonResponse(
            $jsonUsers,
            Response::HTTP_OK,
            [],
            true
        );

    }
}
