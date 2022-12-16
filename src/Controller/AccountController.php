<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/user/account", name="account")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $user = $this->getUser();

        $pw_msg = null;

        $form_password = $this->createForm(NewPasswordType::class, $user);
        $form_password->handleRequest($request);



        if($form_password->isSubmitted() && $form_password->isValid()) {


                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form_password->get('password')->getData()
                    )
                );

                $manager->persist($user);
                $manager->flush();

                $pw_msg = array(
                    "css" => "success",
                    "msg" => "Votre mot de passe a bien été mise à jour !"
                );




        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'form_password' => $form_password->createView(),
            'password_msg' => $pw_msg
        ]);
    }
}
