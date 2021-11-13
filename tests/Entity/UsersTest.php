<?php

namespace App\Tests\Entity;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UsersTest extends KernelTestCase
{
    public function getEntity () : Users {
        return (new Users())
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('johen@doe.fr')
            ->setPassword('password');
    }

    public function assertHasError (Users $user, int $errorExpected = 0) {
        self::bootKernel();
        $error = static::getContainer()->get('validator')->validate($user);
        $this->assertCount($errorExpected, $error);
    }

    public function testValidEntity () {
        $this->assertHasError($this->getEntity());
        $this->assertHasError($this->getEntity()->setFirstname('Kévîn'));
        $this->assertHasError($this->getEntity()->setFirstname('Jean-Christophe'));
    }

    public function testInvalidFirstname () {
       $this->assertHasError($this->getEntity()->setFirstname(''), 1);
       $this->assertHasError($this->getEntity()->setFirstname('Jo4nn'), 1);
       $this->assertHasError($this->getEntity()->setFirstname('4534'), 1);
       $this->assertHasError($this->getEntity()->setFirstname('33'), 2);
       $this->assertHasError($this->getEntity()->setFirstname('ju'), 1);
       $this->assertHasError($this->getEntity()->setFirstname('pseudo avec espace'), 1);
    }
}
