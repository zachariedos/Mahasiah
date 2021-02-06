<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\CartType;
use App\Repository\CartRepository;
use Error;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 * @IsGranted("ROLE_USER")
 */
class CartController extends AbstractController
{

    /**
     * @Route("/", name="cart_show", methods={"GET"})
     */
    public function show(): Response
    {
        $cart = $this->getUser()->getCart();
        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/checkout", name="cart_checkout", methods={"POST"})
     */
    public function checkout(Request $request): Response
    {
        // on recupere le panier de l'utilisateur
        $cart = $this->getUser()->getCart();

        // on recupere les produits dans le panier
        $products = $cart->getCartProducts();

        // on declare la variable du prix total
        $totalPrice = 0;
        foreach ($products as $product) {
            // on aditionne le prix de chaque produit multiplié par leurs quantité dans le panier
            $totalPrice = $totalPrice + ($product->getProduct()->getPrice() * $product->getQuantity());
        }

        // on définit la clé secrete pour pouvoir envoyer la requete de paiement a stripe
        \Stripe\Stripe::setApiKey('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');

        // on créer et on envoie la requete de paiement dans le stripe.js qui envoie la requete a stripe pour créer le paiement
        $paymentIntent = \Stripe\PaymentIntent::create([

            'amount' => $totalPrice * 100,

            'currency' => 'eur',

        ]);

        $output = [

            'clientSecret' => $paymentIntent->client_secret,

        ];
        return new JsonResponse($output);
    }

    /**
     * @Route("/checkout/succes", name="cart_checkout_succes", methods={"POST"})
     */
    public function checkoutsucces(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();

            // on recupere l'utilisateur actuelle donc celui qui a payé le panier
            $user = $this->getUser();

            // on recupere le panier
            $cart = $user->getCart();

            // on recupere les produits du panier
            $products = $cart->getCartProducts();

            // on créer une commande pour cet utilisateur
            $order = new Order;
            $order->setUser($user);
            $response = 0;
            foreach ($products as $product) {
                // on ajoute les produit un par un a la commande
                $orderProduct = new OrderProducts;
                $orderProduct->setProducts($product->getProduct());
                $orderProduct->setQuantity($product->getQuantity());
                $order->addOrderProduct($orderProduct);
                $order->setConfirmeToken(random_int(11111111, 99999999));
                $entityManager->persist($orderProduct);

                // on supprime les articles dont il n'y a plus d'exemplaire apres l'achat et on enleve le nombre d'exemplaire acheté
                $realProduct = $product->getProduct();
                if ($realProduct->getQuantity() == $product->getQuantity()) {
                    $realProduct->setQuantity(0);
                } else {
                    $realProduct->setQuantity($realProduct->getQuantity() - $product->getQuantity());
                    $entityManager->persist($realProduct);
                }

                // on enleve les produits du panier
                $cart->removeCartProduct($product);
            }
            $entityManager->persist($order);
            $entityManager->persist($cart);
            $entityManager->flush();
            
                // envoie d'un email au client pour lui dire que sa commande à bien été prise en compte
                $email = (new Email())
                    ->from('MahasiahBoutique@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Commande sur la boutique')
                    ->html('Votre commande à bien été pris en compte, pour récupérer vos articles en magasin, présentez votre jeton de commande : ' . $order->getConfirmeToken());
                $mailer->send($email);

                // envoie d'un email a l'administrateur pour lui dire qu'une commande à été passé
                $email = (new Email())
                    ->from('MahasiahBoutique@gmail.com')
                    ->to('MahasiahBoutique@gmail.com')
                    ->subject('Commande sur la boutique')
                    ->html('Une <a href="mahasiah.fr/order/'. $order->getId().'">Commande</a> a été passé. ' );
                $mailer->send($email);

        } else {
            return $this->redirectToRoute('products_index');
        }
        return new JsonResponse('Paiement accepté');
    }
}
