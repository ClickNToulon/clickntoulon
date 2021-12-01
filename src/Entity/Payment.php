<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getIcon(int $id): array
    {
        if ($id == 1) {
            return [
                0 => "logo-espèce.png",
                1 => "Espèces"
            ];
        }
        if ($id == 2) {
            return [
                0 => "visa-logo.png",
                1 => "Carte de crédit"
            ];
        }
        if ($id == 3) {
            return [
                0 => "logo_cheque.png",
                1 => "Chèques"
            ];
        }
        if ($id == 4) {
            return [
                0 => "apple-pay.jpg",
                1 => "Apple Pay"
            ];
        }
        if ($id == 5) {
            return [
                0 => "logo-google-pay.png",
                1 => "Google Pay"
            ];
        }
        if ($id == 6) {
            return [
                0 => "bitcoin-logo.png",
                1 => "Bitcoin"
            ];
        }
        if ($id == 7) {
            return [
                0 => "logo-paypal.png",
                1 => "Paypal"
            ];
        }
        return [];
    }
}
