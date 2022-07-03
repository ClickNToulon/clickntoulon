<?php

namespace App\Domain\Product;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[ORM\Entity(repositoryClass: PriceHistoryRepository::class)]
class PriceHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "priceHistory")]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: Types::FLOAT, precision: 2, scale: 2)]
    private ?float $unitPrice;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private int $vat;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $date_start;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $date_end;

    public function __construct()
    {
        $this->date_end = new DateTime('now',  new DateTimeZone("Europe/Paris"));
        $this->date_start = new DateTime('now',  new DateTimeZone("Europe/Paris"));
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return float|null
     */
    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    /**
     * @param float|null $unitPrice
     */
    public function setUnitPrice(?float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @return int
     */
    public function getVat(): int
    {
        return $this->vat;
    }

    /**
     * @param int $vat
     */
    public function setVat(int $vat): void
    {
        $this->vat = $vat;
    }

    /**
     * @return DateTime
     */
    public function getDateStart(): DateTime
    {
        return $this->date_start;
    }

    /**
     * @param DateTime $date_start
     */
    public function setDateStart(DateTime $date_start): void
    {
        $this->date_start = $date_start;
    }

    /**
     * @return DateTime
     */
    public function getDateEnd(): DateTime
    {
        return $this->date_end;
    }

    /**
     * @param DateTime $date_end
     */
    public function setDateEnd(DateTime $date_end): void
    {
        $this->date_end = $date_end;
    }


}