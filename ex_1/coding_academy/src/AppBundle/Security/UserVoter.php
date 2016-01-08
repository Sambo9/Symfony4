<?php

namespace AppBundle\Security;

use CA\BlogBundle\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;


class UserVoter extends Voter
{

    const EDIT = 'edit';
    const VIEW = 'view';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }



    protected function supports($attribute, $subject)
    {

        // only vote on Post objects inside this voter
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Users) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        if (!$user instanceof Users) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Users $edit_user */
        $edit_user = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($edit_user, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }


    private function canEdit(Users $edit_user, Users $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user->getId() === $edit_user->getId();
    }
}

 ?>
