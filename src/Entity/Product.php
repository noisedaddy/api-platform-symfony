<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * THe product
 * Many products to one Manufacturer
 */
#[
    ApiResource(
        paginationItemsPerPage: 5,
        normalizationContext: ['groups' => ['product.read']],
        denormalizationContext: ['groups' => ['product.write']],
        /* we are using this filters with services.yaml config because ApiFilter attribute does not work */
        filters: [
            'product.date_filter',
            'product.search_filter',
            'product.order_filter'
        ],
    ),
//    ApiFilter(
//        SearchFilter::class,
//        properties: [
//            'name'=> SearchFilter::STRATEGY_PARTIAL,
//            'description'=> SearchFilter::STRATEGY_PARTIAL,
//            'manufacturer.countryCode'=> SearchFilter::STRATEGY_EXACT,
//            'manufacturer.id'=> SearchFilter::STRATEGY_EXACT,
//        ]
//    ),
//    ApiFilter(
//        OrderFilter::class,
//        properties: [
//            'issueDate'
//        ]
//    )
]
#[ApiResource(
    uriTemplate: '/manufacturers/{manufacturer_id}/products/{id}',
    uriVariables: [
        'manufacturer_id' => new \ApiPlatform\Metadata\Link(fromClass: Manufacturer::class, toProperty: 'manufacturer'),
        'id' => new \ApiPlatform\Metadata\Link(fromClass: Product::class),
    ],
    operations: [ new Get() ]
)]
#[ApiResource(
    uriTemplate: '/manufacturers/{id}/products',
    uriVariables: [
        'id' => new \ApiPlatform\Metadata\Link(
            fromClass: Manufacturer::class,
            toProperty: 'manufacturer'
        )
    ],
    operations: [new GetCollection()]
)]
#[Get]
#[Post(security: 'is_granted("ROLE_ADMIN")')]
#[Put(security: 'is_granted("ROLE_USER") and object.getOwner() == user', securityMessage: 'A Product can be updated only by a product owner')]
#[ORM\Entity]
class Product
{
    /**
     * ID of product
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     *  mpr of product
     * @var string|null
     */
    #[
        Assert\NotNull,
        Groups(['product.read','product.write'])
    ]
    #[ORM\Column]
    private ?string $mpn = null;

    /**
     * NAME of product
     * @var int|null
     */
    #[
        Assert\NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    #[ORM\Column]
    private string $name = '';

    /**
     * Description of product
     * @var string
     */
    #[
        Assert\NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    #[ORM\Column(type: 'text')]
    private string $description = '';

    /**
     * @var \DateTimeInterface|null the date that manufacturer listed
     */
    #[
        Assert\NotNull,
        Groups(['product.read', 'product.write'])
    ]
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $issueDate = null;

    /**
     * the manufacturer of the product
     * @var Manufacturer|null
     */
    #[
        Groups(['product.read', 'product.write']),
        Assert\NotNull
    ]
    #[ORM\ManyToOne(targetEntity: 'Manufacturer', inversedBy: 'products')]
    private ?Manufacturer $manufacturer = null;

    /**
     * the manufacturer of the product
     * @var User|null
     *
     */
    #[
        Groups(['product.read', 'product.write']),
    ]
    private ?User $owner = null;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

}