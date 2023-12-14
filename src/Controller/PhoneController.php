<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/phones')]
class PhoneController extends AbstractController
{
    #[Route('/', name: 'app_phone', methods: ['GET'])]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 3);

        $phoneList = $phoneRepository->findAllPhonePagined();
        $jsonPhoneList = $serializer->serialize($phoneList, 'json', ['groups' => 'getPhones']);
        return new JsonResponse(
            $jsonPhoneList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/{id}', name: 'app_phone_detail', methods: ['GET'])]
    public function getPhone(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhone = $serializer->serialize($phone, 'json', ['groups' => 'getPhoneDetail']);
        return new JsonResponse(
            $jsonPhone,
            Response::HTTP_OK,
            [],
            true
        );
    }
}
