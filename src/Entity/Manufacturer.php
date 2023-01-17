<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Twig\Node\Expression\GetAttrExpression;

/**
 * THe Manufacturer
 * Many products to one Manufacturer
 */
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Put(),
        new Post(),
        new Patch(),
        new Delete()
    ],
    paginationItemsPerPage: 5
)]
#[ApiResource(
    uriTemplate: '/manufacturers/{id}/products',
    uriVariables: [
        'id' => new Link(
            fromClass: Manufacturer::class,
            fromProperty: 'products'
        )
    ],
    operations: [new GetCollection()]
)]
#[ORM\Entity]
class Manufacturer
{
    /**
     * ID of manufacturer
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * NAME of manufacturer
     * @var int|null
     */
    #[
        Assert\NotBlank,
        Groups(['product.read'])
    ]
    #[ORM\Column]
    private string $name = '';

    /**
     * Description of manufacturer
     * @var string
     */
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private string $description = '';

    /**
     * The country code for manufacturer
     * @var string
     */
    #[Assert\NotBlank]
    #[ORM\Column(length: 3)]
    private string $countryCode = '';

    /**
     * @var \DateTimeInterface|null the date that manufacturer listed
     */
    #[Assert\NotNull]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $listedDate = null;

    /**
     * @var Product[] Available products from this manufacturer
     */
    #[Link(toProperty: 'manufacturer')]
    #[ORM\OneToMany(targetEntity: 'Product', mappedBy: 'manufacturer', cascade: ['persist', 'remove'])] //this is Subresource for /api/manufacturers/{id}/products
    private iterable $products;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return iterable
     */
    public function getProducts(): iterable|ArrayCollection
    {
        return $this->products;
    }

    /**
     * @param iterable $products
     */
    public function setProducts(iterable $products): void
    {
        $this->products = $products;
    }

    /**
     * ID of the manufacturer
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
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getListedDate(): ?\DateTimeInterface
    {
        return $this->listedDate;
    }

    /**
     * @param \DateTimeInterface|null $listedDate
     */
    public function setListedDate(?\DateTimeInterface $listedDate): void
    {
        $this->listedDate = $listedDate;
    }




}