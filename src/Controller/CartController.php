<?php

namespace App\Controller;

use App\Entity\PanierProduit;
use App\Repository\PanierProduitRepository;
use App\Repository\ProduitRepository;
use App\Service\Snackbar\SuccessSnackbar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends BaseController
{
    #[Route('/cart', name: 'cart')]
    public function cart(
        Request $request,
        PanierProduitRepository $ppRepository
    ): Response
    {
        if (!$this->userConnected) {
            $this->saveTargetPath($request->getSession(), 'cart');
            return $this->redirectToRoute('login');
        }

        return $this->render('cart/cart.html.twig', [
            'panierProduits' => $ppRepository->findCartProductsByUserId($this->user->getUserId()),
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/cart/cart-gateway', name: 'cart_gateway')]
    public function cartGateway(
        Request $request,
        PanierProduitRepository $ppRepository
    ): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('home');
        }
        $session = $request->getSession();
        $produit = unserialize($session->get('productAdded'));
        $titreProduit = $request->query->getString('ref');
        if (!$produit || $titreProduit != $produit->getTitle()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('cart/cartGateway.html.twig', [
            'produit' => $produit,
            'nombreArticle' => $this->nombreArticle,
            'prixTotal' => $ppRepository->getTotalCartPrice($this->user->getUserId()),
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/cart/add-to-cart', name: 'add_cart')]
    public function addToCart(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        PanierProduitRepository $panierProduitRepository,
    ): Response
    {
        $idProduit = $request->query->getInt('id');

        // Recherche du produit par identifiant
        $produit = $produitRepository->find($idProduit);
        // Si le produit n'existe pas, redirection sur la page d'accueil
        if (is_null($produit)) {
            throw $this->createNotFoundException();
        } // A partir d'ici, on est sur que le produit existe.

        if ($produit->getQuantityLeft() <= 0) {
            return $this->redirectToRoute('product_sheet', ['ref' => $produit->getTitle()]);
        }

        $session = $request->getSession();
        if (!$this->userConnected) {
            $this->saveTargetPath($session, 'add_cart', ['id' => $idProduit]);
            return $this->redirectToRoute('login');
        }

        $panierProduit = $panierProduitRepository->findOneBy([
            'user' => $this->user->getUserId(),
            'product' => $idProduit
        ]);

        $quantiteVoulu = $request->query->getInt('quantity') ?: 1;
        $quantiteDispo = $produit->getQuantityLeft();

        if (is_null($panierProduit)) {
            // Si le produit n'est pas déjà dans le panier, on créer un nouveau PanierProduit
            $quantiteAjouter = min($quantiteVoulu, $quantiteDispo, 10);
            $panierProduit = new PanierProduit($this->user, $produit, $quantiteAjouter);
        } else {
            // Si le produit est déjà dans le panier, on met à jour la quantité
            $quantiteActuelle = $panierProduit->getQuantityChosen();
            $nouvelleQuantite = min($quantiteActuelle + $quantiteVoulu, $quantiteDispo, 10);
            $panierProduit->setQuantityChosen($nouvelleQuantite);
        }
        $entityManager->persist($panierProduit);
        $entityManager->flush();

        $session->set('productAdded', serialize($produit));

        return $this->redirectToRoute('cart_gateway', [
            'ref' => $produit->getTitle(),
        ]);
    }

    #[Route('/cart/update-cart-quantity', name: 'update_cart_quantity', methods: "POST")]
    public function updateCartQuantity(
        Request $request,
        EntityManagerInterface $entityManager,
        PanierProduitRepository $panierProduitRepository,
    ): JsonResponse
    {
        if (!$this->userConnected) {
            return new JsonResponse([
                'status' => 'error',
                'message' => "Utilisateur non authentifié.",
            ], 401);
        }

        if ($request->isXmlHttpRequest()) {
            $data = json_decode($request->getContent(), true);
            $quantity = $data['quantity'];
            $panierProduit = $panierProduitRepository->findOneBy([
                'user' => $this->user->getUserId(),
                'product' => $data['productId']
            ]);
            if (!is_null($panierProduit)) {
                $panierProduit->setQuantityChosen($quantity);
                $entityManager->flush();
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => "Quantité mise à jour.",
            ]);
        }
        return new JsonResponse([
            'status' => 'error',
            'message' => "Une erreur s'est produite.",
        ], 400);
    }

    #[Route('/cart/delete-from-cart', name: 'delete_from_cart', methods: "POST")]
    public function deleteFromCart(
        Request $request,
        EntityManagerInterface $entityManager,
        PanierProduitRepository $panierProduitRepository,
    ): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $panierProduit = $panierProduitRepository->findOneBy([
            'user' => $this->user->getUserId(),
            'product' => $request->request->getInt('productId')
        ]);

        if (!is_null($panierProduit)) {
            $entityManager->remove($panierProduit);
            $entityManager->flush();
            $this->addSnackbar(new SuccessSnackbar("Article supprimé", "L'article a été supprimé de votre panier."));
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/empty-cart', name: 'empty_cart', methods: "POST")]
    public function emptyCart(EntityManagerInterface $entityManager): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        /** @var PanierProduit $panierProduit */
        foreach ($this->user->getCartProducts() as $panierProduit) {
            $entityManager->remove($panierProduit);
        }
        $entityManager->flush();

        $this->addSnackbar(new SuccessSnackbar("Panier vidé", "Votre panier a été vidé."));

        return $this->redirectToRoute('cart');
    }
}
