<?php

namespace App\Tests\Service;

use App\Entity\Country;
use App\Service\CountryService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CountryServiceTest extends TestCase
{
    public function testSyncCountries(): void
    {
        // Mock HttpClient
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        
        $apiData = [
            [
                'cca3' => 'USA',
                'name' => ['common' => 'United States'],
                'region' => 'Americas',
                'subregion' => 'North America',
                'demonyms' => ['eng' => ['m' => 'American']],
                'population' => 330000000,
                'independent' => true,
                'flags' => ['svg' => 'flag.svg'],
                'currencies' => ['USD' => ['name' => 'United States dollar', 'symbol' => '$']],
            ]
        ];

        $response->expects($this->once())
            ->method('toArray')
            ->willReturn($apiData);

        $httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $this->stringContains('restcountries.com'))
            ->willReturn($response);

        // Mock EntityManager and Repository
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);

        // Mock existing country
        $existingCountry = new Country();
        $existingCountry->setUuid('CAN'); // Canada exists locally but not in API (should be removed)
        
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$existingCountry]);

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Country::class)
            ->willReturn($repository);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Country $country) {
                return $country->getUuid() === 'USA' && $country->getName() === 'United States';
            }));

        $entityManager->expects($this->once())
            ->method('remove')
            ->with($existingCountry);

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new CountryService($httpClient, $entityManager);
        $service->syncCountries();
    }
}
