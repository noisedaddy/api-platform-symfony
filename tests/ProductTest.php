<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductTest extends ApiTestCase
{
    use RefreshDatabaseTrait;


    public function testGetCollection() {

        $response = static::createClient()->request('GET','/api/products');

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

        $response = static::createClient()->request('GET','/api/products?page=2');
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

        static::createClient()->request('POST', '/api/products', [
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

        $client = static::createClient();

        $client->request('PUT', '/api/products/1', [
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

        static::createClient()->request('POST', '/api/products', [
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
}