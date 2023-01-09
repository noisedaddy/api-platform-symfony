<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * THe product
 * Many products to one Manufacturer
 * @ORM\Entity()
 */
#[
    ApiResource,
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name'=> SearchFilter::STRATEGY_PARTIAL,
            'description'=> SearchFilter::STRATEGY_PARTIAL,
            'manufacturer.countryCode'=> SearchFilter::STRATEGY_EXACT,
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'issueDate'
        ]
    )
]
class Product
{
    /**
     * ID of product
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     *  mpr of product
     * @var string|null
     * @ORM\Column()
     */
    #[Assert\NotNull]
    private ?string $mpn = null;

    /**
     * NAME of product
     * @var int|null
     * @ORM\Column()
     */
    #[Assert\NotBlank]
    private string $name = '';

    /**
     * Description of product
     * @var string
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    private string $description = '';

    /**
     * @var \DateTimeInterface|null the date that manufacturer listed
     * @ORM\Column(type="datetime")
     */
    #[Assert\NotNull]
    private ?\DateTimeInterface $issueDate = null;

    /**
     * the manufacturer of the product
     * @var Manufacturer|null
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="products")
     */
    private ?Manufacturer $manufacturer = null;

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer|null $manufacturer
     */
    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }
 
    /**
     * ID of the product
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set name
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    /**
     * @param string|null $mpn
     */
    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTimeInterface|null $listedDate
     */
    public function setIssueDate(?\DateTimeInterface $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

}