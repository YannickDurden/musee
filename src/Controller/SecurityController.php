<?php

    namespace App\Controller;

    use App\Form\LoginType;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
    use Symfony\Component\Routing\Annotation\Route;

    class SecurityController extends Controller
    {
        /**
         * @Route("/login", name="admin_login")
         */
        public function login(Request $request, AuthenticationUtils $authUtils)
        {
            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authUtils->getLastUsername();

            $form = $this->createForm(LoginType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                return $this->redirectToRoute("admin_home");
            } else {
                return $this->render('login.html.twig', array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'form' => $form->createView()
                ));
            }
        }
    }
