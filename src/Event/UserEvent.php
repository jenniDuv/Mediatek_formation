<?php
namespace App\Event;

use App\Entity\User;

class UserEvent
{

    const CONFIRMATION = "user.send.confirmation.email";
    private $user;

    public function __construct(User $user)
    {

        $this->user = $user;

    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

}