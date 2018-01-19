<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminLoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('login.html.twig', [
            // dernier username saisi (si il y en a un)
            'last_username' => $helper->getLastUsername(),
            // La derniere erreur de connexion (si il y en a une)
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/admin", name="admin_login")
     */
    public function loginAdmin(Request $request, SessionInterface $session)
    {
        $form = $this->createForm(AdminLoginType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $userList = $repository->findBy(['firstName' => $user->getFirstName()]);
            if ($userList != null)
            {
                if($userList[0]->getPassword() === $user->getPassword() && $userList[0]->getRole() === "admin" )
                {
                    return new Response("Admin connecté");
                }
            }

            return $this->redirectToRoute('admin_login');


        }
        return $this->render('Back-Office/login.html.twig', [
                'formLogin' => $form->createView()
            ]);
    }

    /**
     * @Route("/addusertest", name="add_user_test")
     */
    public function addUserTest()
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('Admin1', 'Admin2', 'Admin3');
        $em = $this->getDoctrine()->getManager();

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User;

            // Le nom d'utilisateur et le mot de passe sont identiques
            $user->setUsername($name);
            $user->setFirstName($name);
            $user->setLastName($name);
            $user->setEmail($name);
            $user->setNewsletter(false);
            $user->setPassword($name);

            // On définit uniquement le role ROLE_USER qui est le role de base
            $user->setRole(array('ROLE_ADMIN'));

            // On le persiste
            $em->persist($user);
        }

        // On déclenche l'enregistrement
        $em->flush();
        return new Response("ajout fait");
    }

}
