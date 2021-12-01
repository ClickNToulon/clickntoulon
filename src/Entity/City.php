<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private string $codePostal;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private string $codeCommune;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $nomCommune;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $libelleAcheminement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getCodeCommune(): string
    {
        return $this->codeCommune;
    }

    public function setCodeCommune(string $codeCommune): self
    {
        $this->codeCommune = $codeCommune;

        return $this;
    }

    public function getNomCommune(): string
    {
        return $this->nomCommune;
    }

    public function setNomCommune(string $nomCommune): self
    {
        $this->nomCommune = $nomCommune;

        return $this;
    }

    public function getLibelleAcheminement(): string
    {
        return $this->libelleAcheminement;
    }

    public function setLibelleAcheminement(string $libelleAcheminement): self
    {
        $this->libelleAcheminement = $libelleAcheminement;

        return $this;
    }
}
