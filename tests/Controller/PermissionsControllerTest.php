<?php

namespace App\Tests\Controller;


class PermissionsControllerTest extends BaseWebTestCase
{


    public function testGetListOfPermissions()
    {
        $this->login();
        $client = $this->client;
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->accessToken
        ];
        $crawler = $client->request('GET', '/api/permissions', [], [], $headers);

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertJson($client->getResponse()->getContent());
        $resp = json_decode($client->getResponse()->getContent());
        $this->assertNotEmpty($resp, 'Response should be valid JSON');
        $this->assertTrue(is_array($resp));
        $this->assertTrue(count($resp) > 0);

        $this->assertNotEmpty($resp[0]->resource);
        $this->assertNotEmpty($resp[0]->action);
        $this->assertNotEmpty($resp[0]->role);
    }


}
