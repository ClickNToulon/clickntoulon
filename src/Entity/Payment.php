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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

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
     * @return string[]
     */
    public function getIcon(int $id): array
    {
        if ($id == 1) {
            return [
                0 => "fas fa-coins",
                1 => "Espèces"
            ];
        }
        if ($id == 2) {
            return [
                0 => "bi-credit-card-2-front-fill",
                1 => "Carte de crédit"
            ];
        }
        if ($id == 3) {
            return [
                0 => "fas fa-money-check-alt",
                1 => "Chèques"
            ];
        }
        if ($id == 4) {
            return [
                0 => "fab fa-apple-pay",
                1 => "Apple Pay"
            ];
        }
        if ($id == 5) {
            return [
                0 => "fab fa-google-pay",
                1 => "Google Pay"
            ];
        }
        if ($id == 6) {
            return [
                0 => "fab fa-alipay",
                1 => "Ali Pay"
            ];
        }
        if ($id == 7) {
            return [
                0 => "fab fa-amazon-pay",
                1 => "Amazon Pay"
            ];
        }
        if ($id == 8) {
            return [
                0 => "fab fa-bitcoin",
                1 => "Bitcoin"
            ];
        }
        if ($id == 9) {
            return [
                0 => "fab fa-paypal",
                1 => "Paypal"
            ];
        }
    }
}
