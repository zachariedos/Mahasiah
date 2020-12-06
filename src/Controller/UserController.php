<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * @Route("/profil")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="profil_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        return $this->redirectToRoute('profil_edit');
    }

    /**
     * @Route("/edit", name="profil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('index');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/order", name="profil_order", methods={"GET"})
     */
    public function order(Request $request): Response
    {
        $user = $this->getUser();
        $order = $user->getOrders();
        return $this->render('profil/orders.html.twig', [
            'orders' => $order,
        ]);
    }

    /**
     * @Route("/delete", name="profil_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $user = $this->getUser();
        // on supprime l'utilisateur si il n'a pas de commande sinon il ne peut pas supprimer son compte
        if ($user->getOrders()->isEmpty()) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
                return $this->redirectToRoute('index');
            }
        }
        return $this->redirectToRoute('profil_edit');
    }
}
