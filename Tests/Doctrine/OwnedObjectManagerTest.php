<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Tests\Doctrine;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

use Doctrine\Common\Collections\ArrayCollection;

use Bluemesa\Bundle\AclBundle\Doctrine\OwnedObjectManager;
use Bluemesa\Bundle\CoreBundle\Entity\Entity;

class OwnedObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OwnedObjectManager
     */
    private $om;
    private $aclProvider;
    private $userProvider;

    public function testCreateACL()
    {
        $user = new FakeUser();
        $acl = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\MutableAclInterface')->getMock();

        $acl->expects($this->at(0))
            ->method('insertObjectAce')
            ->with(UserSecurityIdentity::fromAccount($user),MaskBuilder::MASK_OWNER);
        $acl->expects($this->at(1))
            ->method('insertObjectAce')
            ->with(new RoleSecurityIdentity('ROLE_TEST'),MaskBuilder::MASK_VIEW);

        $this->aclProvider->expects($this->once())
            ->method('createACL')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($acl));
        $this->aclProvider->expects($this->once())->method('updateAcl')->with($acl);

        $this->om->createACL(new FakeEntity(), array(
            array('identity' => $user,
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_TEST',
                  'permission' => MaskBuilder::MASK_VIEW)));
    }

    public function testCreateACLCollection()
    {
        $user = new FakeUser();
        $entity = new FakeEntity();
        $collection = new ArrayCollection();
        $collection->add($entity);

        $acl = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\MutableAclInterface')->getMock();

        $acl->expects($this->at(0))
            ->method('insertObjectAce')
            ->with(UserSecurityIdentity::fromAccount($user),MaskBuilder::MASK_OWNER);
        $acl->expects($this->at(1))
            ->method('insertObjectAce')
            ->with(new RoleSecurityIdentity('ROLE_TEST'),MaskBuilder::MASK_VIEW);

        $this->aclProvider->expects($this->once())
            ->method('createACL')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($acl));
        $this->aclProvider->expects($this->once())->method('updateAcl')->with($acl);

        $this->om->createACL($collection, array(
            array('identity' => $user,
                  'permission' => MaskBuilder::MASK_OWNER),
            array('identity' => 'ROLE_TEST',
                  'permission' => MaskBuilder::MASK_VIEW)));
    }

    public function testGetOwner()
    {
        $user = new FakeUser();

        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($this->getFakeObjectAces()));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->returnValue($user));

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals($user, $owner);
    }

    public function testGetOwnerNoAcl()
    {
        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->throwException(new AclNotFoundException()));

        $this->userProvider->expects($this->never())
            ->method('loadUserByUsername');

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals(null, $owner);
    }

    public function testGetOwnerNoUser()
    {
        $this->aclProvider->expects($this->once())
            ->method('findAcl')
            ->with($this->isInstanceOf('Symfony\Component\Security\Acl\Domain\ObjectIdentity'))
            ->will($this->returnValue($this->getFakeObjectAces()));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')
            ->will($this->throwException(new UsernameNotFoundException()));

        $owner = $this->om->getOwner(new FakeEntity());
        $this->assertEquals(null, $owner);
    }

    protected function setUp()
    {
        $mr = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')->getMock();;

        $this->aclProvider = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface')->getMock();;
        $this->userProvider = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserProviderInterface')->getMock();;

        $this->om = new OwnedObjectManager();
        $this->om->setManagerRegistry($mr);
        $this->om->setUserProvider($this->userProvider);
        $this->om->setAclProvider($this->aclProvider);
    }

    private function getFakeObjectAces()
    {
        $aces = array();

        $aclEntry_1 = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\EntryInterface')->getMock();
        $aclEntry_1->expects($this->once())
             ->method('getMask')
             ->will($this->returnValue(MaskBuilder::MASK_VIEW));
        $aclEntry_1->expects($this->once())
             ->method('getSecurityIdentity');
        $aces[] = $aclEntry_1;

        $aclEntry_2 = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\EntryInterface')->getMock();
        $aclEntry_2->expects($this->once())
             ->method('getMask')
             ->will($this->returnValue(MaskBuilder::MASK_OWNER));
        $aclEntry_2->expects($this->once())
             ->method('getSecurityIdentity')
             ->will($this->returnValue(
                    new UserSecurityIdentity('user','Bluemesa\Bundle\CoreBundle\Tests\Doctrine\FakeUser')));
        $aces[] = $aclEntry_2;

        $aclInterface = $this->getMockBuilder('Symfony\Component\Security\Acl\Model\AclInterface')->getMock();
        $aclInterface->expects($this->once())
             ->method('getObjectAces')
             ->will($this->returnValue($aces));

        return $aclInterface;
    }

}

class FakeUser implements UserInterface
{
    public function eraseCredentials()
    {

    }

    public function getPassword()
    {
        return 'password';
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        return 'pepper';
    }

    public function getUsername()
    {
        return 'user';
    }
}

class FakeEntity extends Entity
{
    public function getId()
    {
        return rand();
    }
}
