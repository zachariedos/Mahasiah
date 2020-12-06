<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\OrderProducts;
use App\Entity\Products;
use App\Entity\Search;
use App\Form\ProductsType;
use App\Form\SearchType;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request, ProductsRepository $repository): Response
    {

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $Categories = array();
        $products = $repository->findAll();
        foreach ($products as $product) {
            foreach ($product->getCategories() as $Categorie) {
                if (!in_array($Categorie, $Categories)) {
                    $Categories[] = $Categorie;
                }
            }
        }

        $query = $repository->FindBySearch($search);
        $requestedPage = $request->query->getInt('page', 1);
        // Récupération des articles
        $Products = $paginator->paginate(
            $query,             // Requête créée précedemment
            $requestedPage,     // Numéro de la page demandée
            10              // Nombre d'articles affichés par page
        );

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'categories' => $Categories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="products_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productImages = $product->getProductImages();
            foreach ($productImages as $key => $productImage) {
                $productImage->setProduct($product);
                $productImages->set($key, $productImage);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('products_index');
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="products_show", methods={"GET"})
     */
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * @Route("/addtocart/{id}", name="products_addtocart", methods={"POST"})
     */
    public function addToCart(Request $request, Products $product): Response
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $user = $this->getUser();

            // si le produit n'à pas de quantité on le met a un et si il a une quantité définit on recupere cette dernière
            $productquantity = 1;
            $productquantity = $request->request->get('quantity');

            // on ajout au panier seulement si l'utilisateur est connecté
            if ($user) {
                $cart = $user->getCart();

                // si l'utilisateur n'a pas de panier on lui en créé un 
                if (!$cart) {
                    $cart = new Cart;
                    $cart->setUser($user);
                }
                $entityManager = $this->getDoctrine()->getManager();

                // on persist le panier en base de donné
                $entityManager->persist($cart);

                $allproductsalreadyincart = $cart->getCartProducts();
                $checkproductexist = 0;
                foreach ($allproductsalreadyincart as $productalreadyincart) {

                    // Si le produit n'exist pas on met $checkproductexist à 0 sinon si il existe on le met a 1 et on arrete la boucle
                    if (($productalreadyincart->getProduct()->getId()) != $product->getId()) {
                        $checkproductexist = 0;
                    } else {
                        $checkproductexist = 1;
                        break;
                    }
                }

                // Si le produit existe on l'ajoute au panier
                if ($checkproductexist == 0) {

                    // si le produit est encore disponible
                    if ($product->getQuantity() >= $productquantity) {

                        // on ajout le produit et la quantité au panier
                        $cartProduct = new CartProduct;
                        $cartProduct->setCart($cart);
                        $cartProduct->setProduct($product);
                        $cartProduct->setQuantity($productquantity);

                        // on persist le/les produit(s) dans le panier en base de donné
                        $entityManager->persist($cartProduct);
                        $entityManager->flush();
                    }
                }
            }
            return new JsonResponse([]);
        } else {
            return $this->render('products/show.html.twig', [
                'product' => $product,
            ]);
        }
    }

    /**
     * @Route("/removefromcart/{id}", name="products_removefromcart", methods={"POST"})
     */
    public function removeFromCart(Request $request, CartProduct $product): Response
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $user = $this->getUser();
            if ($user) {
                $cart = $user->getCart();
                $entityManager = $this->getDoctrine()->getManager();
                $cart->removeCartProduct($product);
                $entityManager->persist($cart);
                $entityManager->flush();
                return new JsonResponse("reload");
            }
        } else {
            return $this->redirectToRoute('products_index');
        }
    }

    /**
     * @Route("/removefromorder/{id}", name="products_removefromorder", methods={"POST"})
     */
    public function removeFromOrder(Request $request, OrderProducts $product, MailerInterface $mailer): Response
    {
        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            $user = $this->getUser();
            if ($user) {

                $order = $product->getTheorder();
                $customer = $order->getUser();
                $entityManager = $this->getDoctrine()->getManager();
                $order->removeOrderProduct($product);
                $entityManager->persist($order);

                // envoie d'un email au client pour lui dire que son colis à été expédié
                $email = (new Email())
                    ->from('Mahasaiah-Boutique@gmail.com')
                    ->to($customer->getEmail())
                    ->subject('Envoie de votre colis')
                    ->html('Votre colis <br> <b>' . $product->getProducts()->getTitle() . '</b> x ' . $product->getQuantity() . " a été expédié");

                $mailer->send($email);

                if ($order->getOrderProducts()->isEmpty()) {
                    $entityManager->remove($order);
                    $entityManager->flush();
                    return new JsonResponse("orderClosed");
                }
                $entityManager->flush();

                return new JsonResponse("reload");
            }
        } else {
            return $this->redirectToRoute('order_index');
        }
    }

    /**
     * @Route("/{id}/edit", name="products_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Products $product): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productImages = $product->getProductImages();
            foreach ($productImages as $key => $productImage) {
                $productImage->setProduct($product);
                $productImages->set($key, $productImage);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('products_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="products_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Products $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('products_index');
    }
}
