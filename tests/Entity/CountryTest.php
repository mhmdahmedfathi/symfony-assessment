<?php

namespace App\Tests\Entity;

use App\Entity\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $country = new Country();
        
        $country->setUuid('test-uuid');
        $this->assertEquals('test-uuid', $country->getUuid());
        
        $country->setName('Test Country');
        $this->assertEquals('Test Country', $country->getName());
        
        $country->setRegion('Region');
        $this->assertEquals('Region', $country->getRegion());
        
        $country->setSubRegion('SubRegion');
        $this->assertEquals('SubRegion', $country->getSubRegion());
        
        $country->setDemonym('Demonym');
        $this->assertEquals('Demonym', $country->getDemonym());
        
        $country->setPopulation(1000);
        $this->assertEquals(1000, $country->getPopulation());
        
        $country->setIndependant(true);
        $this->assertTrue($country->isIndependant());
        
        $country->setFlag('flag.svg');
        $this->assertEquals('flag.svg', $country->getFlag());
        
        $currency = ['name' => 'Euro', 'symbol' => '€'];
        $country->setCurrency($currency);
        $this->assertEquals($currency, $country->getCurrency());
    }
}
