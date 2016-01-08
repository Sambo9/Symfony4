<?php

namespace AppBundle\Security;


use CA\BlogBundle\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;


class PostVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DELETE = 'delete';
    const EDIT = 'edit';

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

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array("delete", "edit"))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        /** @var Post */
        $post = $subject; // $subject must be a Post instance, thanks to the supports method

        if (!$user instanceof Users) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $post = $subject;

        switch($attribute) {
            case self::DELETE:
                      if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
                          return true;
                      }
                      else {
                        return $this->canDelete($post, $user);
                      }
                      break;

            case self::EDIT:
                      if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
                          return true;
                      }
                      else {
                        return $this->canEdit($post, $user);
                      }
                      break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Post $post, Users $user)
    {
        // if they can edit, they can delete
        if ($this->canEdit($post, $user)) {
            return true;
        }
    }

    private function canEdit(Post $post, Users $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object

        return $user === $post->getUser();
    }
}

 ?>
