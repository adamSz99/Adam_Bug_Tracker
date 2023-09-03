<?php
/**
 * Security controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/login';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test login route.
     */
    public function testLoginRoute(): void
    {
        $email = 'test@example.com';
        $this->createUser(['ROLE_USER'], $email);
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE);
        $form = $crawler->selectButton('Zaloguj')->form();

        $form['email'] = $email;
        $form['password'] = 'user1234';

        $this->httpClient->submit($form);
        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals(302, $result);
    }

    /**
     * Test logout route.
     */
    public function testLogoutRoute(): void
    {
        $createEntityName = 'test@example.com';
        $testUser = $this->createUser(['ROLE_USER'], $createEntityName);

        $this->httpClient->loginUser($testUser);
        $this->httpClient->request('GET', '/logout');

        $result = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals(302, $result);
    }


    /**
     * Create user.
     *
     * @param array  $roles User roles
     * @param string $email Email
     *
     * @return User User entity'
     *
     */
    protected function createUser(array $roles, string $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();

        $user->setEmail($email);
        $user->setRoles($roles);

        $hashedPassword = $passwordHasher->hashPassword($user, 'user1234');

        $user->setPassword($hashedPassword);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }
}
