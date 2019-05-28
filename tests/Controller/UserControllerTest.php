<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserControllerTest extends BaseWebTestCase
{
    public function testGetUsersList()
    {
        $this->login();
        $client = $this->client;
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken
        ];
        $crawler = $client->request('GET', '/api/system/users', [], [], $headers);

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertJson($client->getResponse()->getContent());
        $resp = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($resp, 'Response should be valid JSON');
        $this->assertTrue(is_array($resp));

        $this->assertTrue(count($resp) > 0);

        $this->assertNotEmpty($resp[0]->email);
        $this->assertNotEmpty($resp[0]->roles);

    }
}
