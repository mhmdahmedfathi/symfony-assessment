<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/countries')]
#[OA\Tag(name: 'Countries')]
class CountryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'api_countries_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of countries',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Country::class))
        )
    )]
    public function index(): JsonResponse
    {
        $countries = $this->entityManager->getRepository(Country::class)->findAll();
        
        $data = [];
        foreach ($countries as $country) {
            $data[] = $this->serializeCountry($country);
        }

        return $this->json($data);
    }

    #[Route('', name: 'api_countries_create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'uuid', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'region', type: 'string'),
                // simplified for brevity in example, but full object should be documented
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Country created successfully')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $country = new Country();
        $this->updateCountryFromData($country, $data);

        $errors = $this->validator->validate($country);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($country);
        $this->entityManager->flush();

        return $this->json($this->serializeCountry($country), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_countries_update', methods: ['PATCH'])]
    #[OA\Response(response: 200, description: 'Country updated successfully')]
    public function update(Request $request, int $id): JsonResponse
    {
        $country = $this->entityManager->getRepository(Country::class)->find($id);

        if (!$country) {
            return $this->json(['error' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $this->updateCountryFromData($country, $data);

        $this->entityManager->flush();

        return $this->json($this->serializeCountry($country));
    }

    #[Route('/{id}', name: 'api_countries_delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Country deleted successfully')]
    public function delete(int $id): JsonResponse
    {
        $country = $this->entityManager->getRepository(Country::class)->find($id);

        if (!$country) {
            return $this->json(['error' => 'Country not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($country);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function updateCountryFromData(Country $country, array $data): void
    {
        if (isset($data['uuid'])) $country->setUuid($data['uuid']);
        if (isset($data['name'])) $country->setName($data['name']);
        if (isset($data['region'])) $country->setRegion($data['region']);
        if (isset($data['subRegion'])) $country->setSubRegion($data['subRegion']);
        if (isset($data['demonym'])) $country->setDemonym($data['demonym']);
        if (isset($data['population'])) $country->setPopulation((int)$data['population']);
        if (isset($data['independant'])) $country->setIndependant((bool)$data['independant']);
        if (isset($data['flag'])) $country->setFlag($data['flag']);
        if (isset($data['currency'])) $country->setCurrency($data['currency']);
    }

    private function serializeCountry(Country $country): array
    {
        return [
            'id' => $country->getId(),
            'uuid' => $country->getUuid(),
            'name' => $country->getName(),
            'region' => $country->getRegion(),
            'subRegion' => $country->getSubRegion(),
            'demonym' => $country->getDemonym(),
            'population' => $country->getPopulation(),
            'independant' => $country->isIndependant(),
            'flag' => $country->getFlag(),
            'currency' => $country->getCurrency(),
        ];
    }
}