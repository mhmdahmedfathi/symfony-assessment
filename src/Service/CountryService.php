<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryService
{
    private const API_URL = 'https://restcountries.com/v3.1/all?fields=cca3,name,region,subregion,demonyms,population,independent,flags,currencies';

    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function syncCountries(): void
    {
        $response = $this->httpClient->request('GET', self::API_URL);
        $data = $response->toArray();
        $repo = $this->entityManager->getRepository(Country::class);
        
        // Fetch all existing countries to handle updates and deletions efficiently
        $existingCountries = $repo->findAll();
        $countryMap = [];
        foreach ($existingCountries as $country) {
            $countryMap[$country->getUuid()] = $country;
        }

        $processedUuids = [];

        foreach ($data as $countryData) {
            // Use alpha3Code (cca3) as UUID
            $uuid = $countryData['cca3'] ?? null;
            if (!$uuid) {
                continue;
            }

            $currentCountry = $countryMap[$uuid] ?? new Country();
            $this->mapDataToEntity($countryData, $currentCountry);
            
            if (!isset($countryMap[$uuid])) {
                $this->entityManager->persist($currentCountry);
            }
            
            $processedUuids[] = $uuid;
        }

        // Remove countries that are not in the api anymore
        foreach ($countryMap as $uuid => $country) {
            if (!in_array($uuid, $processedUuids)) {
                $this->entityManager->remove($country);
            }
        }

        $this->entityManager->flush();
    }

    private function mapDataToEntity(array $data, Country $country): void
    {
        $country->setUuid($data['cca3'] ?? '');
        $country->setName($data['name']['common'] ?? 'Unknown');
        $country->setRegion($data['region'] ?? 'Unknown');
        $country->setSubRegion($data['subregion'] ?? 'Unknown');
        
        // Handle demonyms safely
        $demonym = 'Unknown';
        if (isset($data['demonyms']['eng']['m'])) {
            $demonym = $data['demonyms']['eng']['m'];
        }
        $country->setDemonym($demonym);

        $country->setPopulation($data['population'] ?? 0);
        $country->setIndependant($data['independent'] ?? false);
        $country->setFlag($data['flags']['svg'] ?? ($data['flags']['png'] ?? ''));

        // Handle currencies
        $currencyData = [];
        if (isset($data['currencies']) && is_array($data['currencies'])) {
            $firstCurrency = reset($data['currencies']);
            if ($firstCurrency) {
                $currencyData = [
                    'name' => $firstCurrency['name'] ?? '',
                    'symbol' => $firstCurrency['symbol'] ?? ''
                ];
            }
        }
        $country->setCurrency($currencyData);
    }
}
