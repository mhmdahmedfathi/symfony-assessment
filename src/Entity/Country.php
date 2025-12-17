<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[OA\Schema]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private string $uuid;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $region;

    #[ORM\Column(type: 'string')]
    private string $subRegion;

    #[ORM\Column(type: 'string')]
    private string $demonym;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero]
    private int $population;

    #[ORM\Column(type: 'boolean')]
    private bool $independant;

    #[ORM\Column(type: 'string')]
    private string $flag;

    #[ORM\Column(type: 'json')]
    private array $currency = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getSubRegion(): string
    {
        return $this->subRegion;
    }

    public function setSubRegion(string $subRegion): void
    {
        $this->subRegion = $subRegion;
    }

    public function getDemonym(): string
    {
        return $this->demonym;
    }

    public function setDemonym(string $demonym): void
    {
        $this->demonym = $demonym;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    public function isIndependant(): bool
    {
        return $this->independant;
    }

    public function setIndependant(bool $independant): void
    {
        $this->independant = $independant;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): void
    {
        $this->flag = $flag;
    }

    public function getCurrency(): array
    {
        return $this->currency;
    }

    public function setCurrency(array $currency): void
    {
        $this->currency = $currency;
    }
}