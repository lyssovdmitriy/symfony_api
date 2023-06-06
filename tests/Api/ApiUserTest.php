<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** Integration tests */
class ApiUserTest extends WebTestCase
{

    public function testCreateUser(): void
    {
        $client = static::createClient();

        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');

        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'birthday' => '1980-01-25',
            'phone' => '+98123456789',
            'address' => 'Mozart street 1',
        ];

        $client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($userData));
        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(true, $responseData['success']);

        $user = $userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($user);

        $userRepository->remove($user, true);
    }

    public function testGetUser(): void
    {

        $client = static::createClient();

        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');

        $user = new User();
        $user->setEmail('test2@example.com');
        $user->setPassword('pass');
        $user->setBirthday(new DateTime());
        $user->setPhone('+98123456789');
        $user->setAddress('Mozart street 1');

        $userRepository->save($user, true);

        $encoder = $this->getContainer()->get('lexik_jwt_authentication.encoder');

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $token = $encoder->encode($userData);

        $client->request('GET', '/api/users/' . $user->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals($user->getEmail(), $responseData['data']['email']);

        $userRepository->remove($user, true);
    }

    public function testPutUser(): void
    {

        $client = static::createClient();

        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');

        $user = new User();
        $user->setEmail('test2@example.com');
        $user->setPassword('pass');
        $user->setBirthday(new DateTime());
        $user->setPhone('+98123456789');
        $user->setAddress('Mozart street 1');

        $userRepository->save($user, true);

        $encoder = $this->getContainer()->get('lexik_jwt_authentication.encoder');

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $token = $encoder->encode($userData);

        $userData = [
            'phone' => 'test_phone',
            'address' => 'test_address',
        ];

        $client->request('PUT', '/api/users/' . $user->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . $token,
        ], json_encode($userData));

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);

        $user = $userRepository->findOneBy(['id' => $user->getId()]);

        $this->assertEquals($userData['phone'], $user->getPhone());
        $this->assertEquals($userData['address'], $user->getAddress());

        $userRepository->remove($user, true);
    }

    public function testDeleteUser(): void
    {

        $client = static::createClient();

        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');

        $user = new User();
        $user->setEmail('testDel@example.com');
        $user->setPassword('pass');
        $user->setBirthday(new DateTime());
        $user->setPhone('+98123456789');
        $user->setAddress('Mozart street 1');

        $userRepository->save($user, true);

        $encoder = $this->getContainer()->get('lexik_jwt_authentication.encoder');

        $userData = [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $token = $encoder->encode($userData);

        $client->request('DELETE', '/api/users/' . $user->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);

        $user = $userRepository->findOneBy(['id' => $user->getId()]);
        $this->assertEquals(null, $user);
    }

    public function testGetUserList(): void
    {

        $client = static::createClient();

        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');

        $user = new User();
        $user->setEmail('test2@example.com');
        $user->setPassword('pass');
        $user->setBirthday(new DateTime());
        $user->setPhone('+98123456789');
        $user->setAddress('Mozart street 1');


        $userRepository->save($user);

        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('pass');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setBirthday(new DateTime());
        $admin->setPhone('+98123456789');
        $admin->setAddress('Mozart street 1');


        $userRepository->save($admin, true);

        $encoder = $this->getContainer()->get('lexik_jwt_authentication.encoder');

        $userData = [
            'email' => $admin->getEmail(),
            'roles' => $admin->getRoles(),
        ];

        $token = $encoder->encode($userData);

        $client->request('GET', '/api/users', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals(2, count($responseData['data']));

        $userRepository->remove($user);
        $userRepository->remove($admin, true);
    }
}
