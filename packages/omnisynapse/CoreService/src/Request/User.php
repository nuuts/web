<?php

namespace OmniSynapse\CoreService\Request;

/**
 * Class UserCreatedRequest
 * @package OmniSynapse\CoreService\Request
 */
class User implements \JsonSerializable
{
    /** @var string */
    public $userId;

    /** @var string */
    public $username;

    /** @var string */
    public $referrerId;

    /**
     * User constructor.
     *
     * @param \App\Models\User $user
     */
    public function __construct(\App\Models\User $user)
    {
        $this->setUserId($user->getId())
            ->setUsername($user->getName())
            ->setReferrerId($user->getReferrer());
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'            => $this->userId,
            'username'      => $this->username,
            'referrer_id'   => $this->referrerId,
        ];
    }

    /**
     * @param string $userId
     * @return User
     */
    public function setUserId(string $userId): User
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param \App\Models\User $referrer
     * @return User
     */
    public function setReferrerId(\App\Models\User $referrer=null): User
    {
        $this->referrerId = null !== $referrer
            ? $referrer->getId()
            : null;
        return $this;
    }
}
