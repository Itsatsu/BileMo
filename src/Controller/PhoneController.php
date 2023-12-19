<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api/phones')]
class PhoneController extends AbstractController
{


    /**
     * @OA\Response(
     *      response=200,
     *      description="Retourne la listes des téléphones",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Phone::class, groups={"getPhones"}))
     *      )
     *)
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Numéro de la page",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Nombre d'éléments par page",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="brand",
     *     in="query",
     *     description="Nom de la marque",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Phones")
     *
     *
     */
    #[Route('/', name: 'app_phone', methods: ['GET'])]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 3);
        $brand = $request->query->get('brand', null);

        $idCache = 'phones-list-' . $page . '-' . $limit . '-' . $brand;
        $jsonPhoneList = $cache->get($idCache, function (ItemInterface $item) use ($phoneRepository, $page, $limit, $brand, $serializer) {

            $item->tag('phonesCache');
            $item->expiresAfter(60);
            $phoneList = $phoneRepository->findAllPhonePagined($page, $limit, $brand);
            $context = SerializationContext::create()->setGroups(['getPhones']);
            return $serializer->serialize($phoneList, 'json', $context);
        });

        return new JsonResponse(
            $jsonPhoneList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @OA\Response(
     *      response=200,
     *      description="Retourne un téléphone avec ces caractéristiques",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Phone::class, groups={"getPhoneDetail"}))
     *      )
     *)
     * @OA\Tag(name="Phones")
     */
    #[Route('/{id}', name: 'app_phone_detail', methods: ['GET'])]
    public function getPhone(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getPhoneDetail']);
        $jsonPhone = $serializer->serialize($phone, 'json', $context);
        return new JsonResponse(
            $jsonPhone,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
