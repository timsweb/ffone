<?php
namespace Fone;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface
{
    protected $_connection;

    public function __construct(Connection $conn)
    {
        $this->_connection = $conn;
    }

    public function loadUserByUsername($username)
    {
        $stmt = $this->_connection->executeQuery('SELECT * FROM users WHERE name = ?', array(strtolower($username)));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new User($user['id'], $user['name'], $user['password'], ['ROLE_USER'], true, true, true, true);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class instanceof User;
    }
}