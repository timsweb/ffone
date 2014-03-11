<?php

namespace Fone\Model;
/**
 *
 */
class User extends AbstractModel implements \Symfony\Component\Security\Core\User\UserInterface
{

    protected $_roles = [];

    public function getid()
    {
        return $this->_get('id');
    }

    public function setid($id)
    {
        $this->_set('id', $id);
        return $this;
    }

    public function getname()
    {
        return $this->_get('name');
    }

    public function setname($name)
    {
        $this->_set('name', $name);
        return $this;
    }

    public function getpassword()
    {
        return $this->_get('password');
    }

    public function setpassword($password)
    {
        $this->_set('password', $password);
        return $this;
    }

    public function eraseCredentials() {}

    public function getRoles()
    {
        return $this->_roles;
    }

    public function setRoles(array $roles)
    {
        $this->_roles = $roles;
        return $this;
    }

    public function getSalt() {}

    public function getUsername()
    {
        return $this->getname();
    }

}