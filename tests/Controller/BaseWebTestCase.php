<?php
/**
 * Created by PhpStorm.
 * User: Alexey Itsekson
 * Date: 01.02.2019
 * Time: 9:19
 */

namespace App\Tests\Controller;


use App\Entity\User;
use App\Security\OAth2TokenServiceInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BaseWebTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;
    protected $accessToken;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    protected function login()
    {
        /** @var OAth2TokenServiceInterface $jwtService */
        $jwtService = self::$kernel->getContainer()->get(OAth2TokenServiceInterface::class);
        $this->assertTrue($jwtService instanceof OAth2TokenServiceInterface);
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_ADMIN']);
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $accessToken = $jwtService->createAccessToken($token);
        $this->assertNotEmpty($accessToken);
        $this->accessToken = $accessToken;
    }

    protected function createHttpClientMock($respJson): ClientInterface {
        $bodyStream = $this->createMock(StreamInterface::class);
        $bodyStream->expects($this->once())
            ->method('getContents')
            ->willReturn($respJson);

        $resp = $this->createMock(Response::class);
        $resp->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($bodyStream));

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('request')
            ->will($this->returnValue($resp));

        self::$kernel->getContainer()->set(ClientInterface::class, $client);

        return $client;

    }

}