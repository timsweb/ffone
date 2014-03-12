<?php
namespace Fone\Mapper;
use Fone\Model\User as UserModel;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class User extends AbstractMapper implements UserProviderInterface
{

    public function loadUserByUsername($username)
    {
        $user = $this->get(['name' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        return $user;
    }

    protected function _hydrate($row)
    {
        $model = parent::_hydrate($row);
        if ($model) {
            $model->setRoles(['ROLE_USER']);
        }
        return $model;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserModel) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class instanceof UserModel;
    }
}