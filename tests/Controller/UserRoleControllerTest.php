<?php

namespace App\Tests\Controller;

class UserRoleControllerTest extends BaseWebTestCase
{
    public function testGetRolesList()
    {
        $this->login();
        $client = $this->client;
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken
        ];
        $crawler = $client->request('GET', '/api/user-roles/full', [], [], $headers);

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertJson($client->getResponse()->getContent());
        $resp = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($resp, 'Response should be valid JSON');
        $this->assertTrue(is_array($resp));
        $this->assertTrue(count($resp) > 0, 'returned not empty array of roles');

        $this->assertNotEmpty($resp[0]->name, 'name not empty');
        $this->assertTrue(isset($resp[0]->parent_roles) && is_array($resp[0]->parent_roles), 'parent_roles present');
    }
}
