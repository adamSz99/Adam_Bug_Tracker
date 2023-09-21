<?php
/**
 * User role test.
 */

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;


/**
 * Class UserRoleTest.
 */
class UserRoleTest extends TestCase
{
    /**
     * Test user label
     *
     * @return void Void
     */
    public function testLabel(): void
    {
        self::assertEquals('label.role_user', UserRole::ROLE_USER->label());
        self::assertEquals('label.role_admin', UserRole::ROLE_ADMIN->label());
    }
}
