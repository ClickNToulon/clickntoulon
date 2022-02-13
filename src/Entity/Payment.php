<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
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

    public function getIcon(int $id): array
    {
        if ($id == 1) {
            return [
                0 => "cash-payment.png",
                1 => "cash-payment.png",
                2 => "Espèces"
            ];
        }
        if ($id == 2) {
            return [
                0 => "visa.svg",
                1 => "visa-dark.svg",
                2 => "Carte de crédit"
            ];
        }
        if ($id == 3) {
            return [
                0 => "cheque.png",
                1 => "cheque.png",
                2 => "Chèques"
            ];
        }
        if ($id == 4) {
            return [
                0 => "apple-pay.svg",
                1 => "apple-pay-dark.svg",
                2 => "Apple Pay"
            ];
        }
        if ($id == 5) {
            return [
                0 => "google-pay.svg",
                1 => "google-pay-dark.svg",
                2 => "Google Pay"
            ];
        }
        if ($id == 6) {
            return [
                0 => "bitcoin.svg",
                1 => "bitcoin-dark.svg",
                2 => "Bitcoin"
            ];
        }
        if ($id == 7) {
            return [
                0 => "paypal.svg",
                1 => "paypal-dark.svg",
                2 => "Paypal"
            ];
        }
        return [];
    }
}
