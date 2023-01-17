<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private const API_TOKEN = '0c091b385f4318975eb77a607914cfb9eed47b0ac6b7b445a49c6e0949483dd552865d7d2740e6f5a39ff8cc80e8672f114ddb2418d39bfbe63d01b3';
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('veljko.radenkovic@gmail.com');
        $user->setPassword('123456789');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $apiToken = new ApiToken();
        $apiToken->setToken(self::API_TOKEN);
        $apiToken->setUser($user);
        $this->entityManager->persist($apiToken );
        $this->entityManager->flush();

    }

    public function testGetCollection() {

        $response = $this->client->request('GET','/api/products', [
            'headers' => ['x-api-token' => self::API_TOKEN]
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            "@context" =>"/api/contexts/Product",
            "@id"=> "/api/products",
            "@type"=> "hydra:Collection",
            "hydra:totalItems"=> 100,
            "hydra:view"=> [
                "@id"=> "/api/products?page=1",
                "@type"=> "hydra:PartialCollectionView",
                "hydra:first"=> "/api/products?page=1",
                "hydra:last"=> "/api/products?page=20",
                "hydra:next"=> "/api/products?page=2"
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);

    }


    public function testPagination() {

        $response = $this->client->request('GET','/api/products?page=2', [
            'headers' => ['x-api-token' => self::API_TOKEN]
        ]);

        $this->assertJsonContains([
            "@context" =>"/api/contexts/Product",
            "@id"=> "/api/products",
            "@type"=> "hydra:Collection",
            "hydra:totalItems"=> 100,
            "hydra:view"=> [
                "@id"=> "/api/products?page=2",
                "@type"=> "hydra:PartialCollectionView",
                "hydra:first"=> "/api/products?page=1",
                "hydra:last"=> "/api/products?page=20",
                "hydra:previous"=> "/api/products?page=1",
                "hydra:next"=> "/api/products?page=3"
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }


    public function testCreateProduct(): void {

        $this->client->request('POST', '/api/products', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product',
                'description' => 'A Test Description',
                'issueDate' => '1985-07-31',
                'manufacturer' => '/api/manufacturers/1',
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            'mpn'         => '1234',
            'name'        => 'A Test Product',
            'description' => 'A Test Description',
            'issueDate'   => '1985-07-31T00:00:00+00:00'
        ]);

    }

    public function testUpdateProduct(): void {

        $this->client->request('PUT', '/api/products/1', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json' => [
                '@id' => 'api/products/1',
                'description' => 'A Test Description update',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'description' => 'A Test Description update',
        ]);
    }

    public function testCreateInvalidProduct(): void {

        $this->client->request('POST', '/api/products', [
            'headers' => ['x-api-token' => self::API_TOKEN],
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product',
                'description' => 'A Test Description',
                'issueDate' => '1985-07-31',
                'manufacturer' => null,
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context'         => '/api/contexts/ConstraintViolationList',
            '@type'        => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description'   => 'manufacturer: This value should not be null.'
        ]);
    }

    public function testInvalidToken(): void {

        $this->client->request('PUT', '/api/products/1', [
            'headers' => ['x-api-token' => 'fake-token'],
            'json' => [
                '@id' => 'api/products/1',
                'description' => 'A Test Description update',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);

        $this->assertJsonContains([
            'message' => 'Invalid credentials.',
        ]);
    }

}