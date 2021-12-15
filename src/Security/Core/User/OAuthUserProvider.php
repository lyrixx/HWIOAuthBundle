<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 */
final class OAuthUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new OAuthUser($identifier);
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * @return UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        return $this->loadUserByIdentifier($response->getNickname());
    }

    /**
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(\get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', \get_class($user)));
        }

        // @phpstan-ignore-next-line Symfony <5.4 BC layer
        $username = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername();

        return $this->loadUserByUsername($username);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'HWI\\Bundle\\OAuthBundle\\Security\\Core\\User\\OAuthUser' === $class;
    }
}