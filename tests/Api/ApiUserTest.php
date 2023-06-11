<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** Integration tests */
class ApiUserTest extends WebTestCase
{

    static KernelBrowser $client;
    private UserRepository $userRepository;
    private EntityManager $em;
    private User $user;
    private $userToken;
    private $encoder;

    public function setUp(): void
    {
        static::$client = static::createClient();

        $container = $this->getClient()->getContainer();

        /** @var \Doctrine\ORM\EntityManager */
        $this->em = $container->get('doctrine')->getManager();

        $this->em->getConnection()->executeQuery('TRUNCATE public.user CASCADE');

        $this->userRepository = $this->em->getRepository(User::class);

        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setPassword('pass');
        $this->user->setBirthday(new DateTime());
        $this->user->setPhone('+98123456789');
        $this->user->setAddress('Mozart street 1');

        $this->userRepository->save($this->user, true);

        $this->encoder = $container->get('lexik_jwt_authentication.encoder');

        $userData = [
            'email' => $this->user->getEmail(),
            'roles' => $this->user->getRoles(),
        ];

        $this->userToken = $this->encoder->encode($userData);
    }

    private function getClient()
    {
        return static::$client;
    }

    public function testCreateUser(): void
    {
        $email = 'test@create.com';
        $userData = [
            'email' => $email,
            'password' => 'password123',
            'birthday' => '1980-01-25',
            'phone' => '+98123456789',
            'address' => 'Mozart street 1',
        ];

        $this->getClient()->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($userData));
        $response = $this->getClient()->getResponse();

        $this->assertEquals(201, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(true, $responseData['success']);

        $user = $this->userRepository->findOneBy(['email' => $email]);
        $this->assertNotNull($user);
    }

    public function testGetUser(): void
    {
        $this->getClient()->request('GET', '/api/users/' . $this->user->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->userToken,
        ]);

        $response = $this->getClient()->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals($this->user->getEmail(), $responseData['data']['email']);
    }

    public function testPutUser(): void
    {
        $userData = [
            'phone' => 'test_phone',
            'address' => 'test_address',
        ];

        $this->getClient()->request('PUT', '/api/users/' . $this->user->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $this->userToken,
        ], json_encode($userData));

        $response = $this->getClient()->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);

        $user = $this->userRepository->findOneBy(['id' => $this->user->getId()]);

        $this->assertEquals($userData['phone'], $user->getPhone());
        $this->assertEquals($userData['address'], $user->getAddress());
    }

    public function testDeleteUser(): void
    {
        $this->getClient()->request('DELETE', '/api/users/' . $this->user->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->userToken,
        ]);

        $response = $this->getClient()->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);

        $user = $this->userRepository->findOneBy(['id' => $this->user->getId()]);
        $this->assertEquals(null, $user);
    }

    public function testGetUserList(): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('pass');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setBirthday(new DateTime());
        $admin->setPhone('+98123456789');
        $admin->setAddress('Mozart street 1');

        $this->userRepository->save($admin, true);

        $token = $this->encoder->encode([
            'email' => $admin->getEmail(),
            'roles' => $admin->getRoles(),
        ]);

        $this->getClient()->request('GET', '/api/users', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response = $this->getClient()->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals(2, count($responseData['data']));
    }
}
