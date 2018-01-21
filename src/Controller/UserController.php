<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/addusertest", name="add_user_test")
     */
    public function addUserTest()
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('Admin1', 'Admin2', 'Admin3');
        $em = $this->getDoctrine()->getManager();

        foreach ($listNames as $name) {

            $user = new User;

            $user->setUsername($name);
            $user->setFirstName($name);
            $user->setLastName($name);
            $user->setEmail($name);
            $user->setNewsletter(false);
            $passHash = password_hash($name, PASSWORD_BCRYPT);
            $user->setPassword($passHash);

            $user->setRole(array('ROLE_ADMIN'));

            $em->persist($user);
        }

        // On déclenche l'enregistrement
        $em->flush();
        return new Response("ajout fait");
    }

}
