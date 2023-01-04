<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * THe product
 * Many products to one Manufacturer
 * @ORM\Entity()
 */
#[ApiResource]
class Product
{
    /**
     * ID of manifacturer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     *  mpr of manufacturer
     * @var string|null
     * @ORM\Column()
     */
    private ?string $mpn = null;

    /**
     * NAME of manifacturer
     * @var int|null
     * @ORM\Column()
     */
    private string $name = '';

    /**
     * Description of manufacturer
     * @var string
     * @ORM\Column(type="text")
     */
    private string $description = '';

    /**
     * @var \DateTimeInterface|null the date that manufacturer listed
     * @ORM\Column(type="datetime")
     */
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
     * ID of the Manifacturer
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