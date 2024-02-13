<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductSheetController extends BaseController
{
    #[Route('/product', name: 'product_sheet')]
    public function product(
        Request $request,
        ProduitRepository $produitRepository,
    ): Response
    {
        $produitTitle = $request->query->getString('ref');
        $produit = $produitRepository->findOneBy(['title' => $produitTitle]);

        if (is_null($produit)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('product_sheet.html.twig', [
            'produit' => $produit,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }
}
