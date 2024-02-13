<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Service\Sort\SortBy;
use App\Service\Sort\SortOrder;
use App\Service\Sort\SortType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends BaseController
{
    const limit = 15;

    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly PaginatorInterface $paginator
    ) {}

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        /* Récupération de la valeur du champ
        La fonction `trim` enlève les espaces au début et à la fin de la chaine.
        Cela empêche une erreur SQL */
        $term = trim($request->query->get('term'));
        $term = strip_tags($term);

        // Filtre les caractères spéciaux tout en conservant les lettres accentuées
        $cleanedTerm = preg_replace('/[^a-zA-Z0-9\sáéíóúÁÉÍÓÚàèìòùÀÈÌÒÙäëïöüÄËÏÖÜâêîôûÂÊÎÔÛ]/', '', $term);


        // Remplace les multiples espaces par un seul espace
        $cleanedTerm = preg_replace('/\s+/', ' ', $cleanedTerm);


        // Si la requête est vide, on redirige vers la page d'accueil
        // Ce n'est pas censé arriver, le champ est en require.
        // Cependant on peut toujours y accéder via l'url.
        if ($term == "") {
            return $this->redirectToRoute('home');
        }

        // Si le terme nettoyé est différent de l'original,
        // rediriger vers la même route avec le terme nettoyé
        if ($cleanedTerm !== $term) {
            return $this->redirectToRoute('search', ['term' => $cleanedTerm]);
        }

        $page = $request->query->getInt('page', 1);
        $path = $request->getPathInfo();
        $params = $request->query->all();
        if ($redirect = $this->verifyPageNumberBefore0(
            $page,
            $path,
            $params
        )) {
            return $redirect;
        }

        $terms = explode(' ', iconv('UTF-8', 'ASCII//TRANSLIT', $term));

        // Effectuer une éventuelle correction orthographique ici.

        $allShingles = $this->getAllShinglesFromList($terms);

        $sortName = $request->query->getString('s');

        if ($sortName == 'pertinence' || $sortName == '') {
            $listeProduits = $this->produitRepository->findByTitleContaining($allShingles);
            $listeProduits = $this->trierProduitsParPertinence($listeProduits, $terms, $allShingles);
        } else {
            $listeProduits = $this->produitRepository->findByTitleContaining(
                $allShingles,
                $this->getSort($sortName)
            );
        }

        $pagination = $this->paginator->paginate(
            $listeProduits,
            $page,
            self::limit
        );

        if ($redirectResponse = $this->verifyPageNumberAfterMax(
            $page,
            $path,
            $params,
            $pagination->getTotalItemCount()
        )) {
            return $redirectResponse;
        }

        return $this->render('search/search.html.twig', [
            'recherche' => $term,
            'listeProduits' => $pagination,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/{categoryHrefName}', name: 'search-by-category', priority: -1)]
    public function searchByCategory(string $categoryHrefName, Request $request): Response
    {
        if (!isset($this->categories[$categoryHrefName])) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->getInt('page', 1);
        $params = $request->query->all();
        $path = $request->getPathInfo();
        if ($redirect = $this->verifyPageNumberBefore0(
            $page,
            $path,
            $params
        )) {
            return $redirect;
        }

        $sortName = $request->query->getString('s');
        $sortBy = $this->getSort($sortName);

        $listeProduits = $this->produitRepository->findAllBySubCategories(
            $this->subCategories[$categoryHrefName],
            $sortBy
        );

        $pagination = $this->paginator->paginate(
            $listeProduits,
            $page,
            self::limit
        );

        if ($redirectResponse = $this->verifyPageNumberAfterMax(
            $page,
            $path,
            $params,
            $pagination->getTotalItemCount()
        )) {
            return $redirectResponse;
        }

        return $this->render('search/search.html.twig', [
            'recherche' => "",
            'listeProduits' => $pagination,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/{categoryHrefName}/{subCategoryHrefName}', name: 'search-by-sub-category', priority: -1)]
    public function searchBySubCategory(
        string $categoryHrefName,
        string $subCategoryHrefName,
        Request $request
    ): Response
    {
        if (!isset($this->categories[$categoryHrefName])) {
            throw $this->createNotFoundException();
        }

        if (!isset($this->subCategories[$categoryHrefName][$subCategoryHrefName])) {
            throw $this->createNotFoundException();
        }

        $path = $request->getPathInfo();
        $page = $request->query->getInt('page', 1);
        $params = $request->query->all();
        if ($redirect = $this->verifyPageNumberBefore0(
            $page,
            $path,
            $params
        )) {
            return $redirect;
        }

        $sortName = $request->query->getString('s');
        $sortBy = $this->getSort($sortName);

        $listeProduits = $this->produitRepository->findAllBySubCategories(
            [$this->subCategories[$categoryHrefName][$subCategoryHrefName]],
            $sortBy
        );

        $pagination = $this->paginator->paginate(
            $listeProduits,
            $page,
            self::limit
        );

        if ($redirectResponse = $this->verifyPageNumberAfterMax(
            $page,
            $path,
            $params,
            $pagination->getTotalItemCount()
        )) {
            return $redirectResponse;
        }

        return $this->render('search/search.html.twig', [
            'recherche' => "",
            'listeProduits' => $pagination,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    /**
     * Vérifie si le numéro de la page n'est pas inférieur ou égal à 0.
     * Si c'est le cas, alors on renvoie une réponse de redirection sans
     * le paramètre 'page', sinon on renvoie null.
     * @param int $page
     * @param string $path
     * @param array $params
     * @return RedirectResponse|null
     */
    private function verifyPageNumberBefore0(int $page, string $path, array $params): ?RedirectResponse {
        if ($page <= 0) {
            unset($params['page']);
            if (count($params) == 0) {
                return $this->redirect($path);
            }
            return $this->redirect($path.'?'.http_build_query($params));
        }
        return null;
    }

    /**
     * Vérifie si le numéro de la page ne dépasse pas la page maximal.
     * Si c'est le cas, alors on renvoie une réponse de redirection avec
     * le paramètre 'page' qui vaux la page max, sinon on renvoie null.
     * @param int $page
     * @param string $path
     * @param array $params
     * @param int $totalItemCount
     * @return RedirectResponse|null
     */
    private function verifyPageNumberAfterMax(int $page, string $path, array $params, int $totalItemCount): ?RedirectResponse {
        if ($page > $calculated = (int) ($totalItemCount / self::limit + 1)) {
            $params['page'] = $calculated;
            if (count($params) == 0) {
                return $this->redirect($path);
            }
            return $this->redirect($path.'?'.http_build_query($params));
        }
        return null;
    }

    /**
     * Obtiens un objet de tri en fonction du sortName
     * @param string $sortName
     * @return SortBy
     */
    private function getSort(string $sortName): SortBy
    {
        return match ($sortName) {
            'price-asc' => new SortBy(SortType::byPrice),
            'price-desc' => new SortBy(SortType::byPrice, SortOrder::DESC),
            default => new SortBy(SortType::byTitle) // Default is order by title ASC
        };
    }

    /**
     * Crée un ensemble de shingles (sous-chaînes) pour un mot donné.
     *
     * Cette fonction génère des shingles de longueurs 3 à la longueur du mot concerné
     * pour chaque sous-section du mot donné. Elle inclut également le mot entier
     * comme un de ses shingles.
     *
     * @param string $word Le mot pour lequel générer des shingles.
     * @return array Un tableau de shingles uniques générés à partir du mot.
     */
    private function createShingles(string $word): array {
        $shingles = [];
        $length = strlen($word);
        if ($length > 20) { // Limité à 20
            $max = 20;
        } else {
            $max = $length;
        }

        $shingles[] = $word;

        // Génère des shingles pour chaque sous-section du mot
        for ($i = 0; $i < $length; $i++) {
            for ($j = 3; $j <= $max; $j++) {
                if ($i + $j <= $max) {
                    $shingles[] = substr($word, $i, $j);
                }
            }
        }

        return array_unique($shingles); // Élimine les doublons
    }

    /**
     * Génère des shingles pour une liste de mots.
     *
     * Cette fonction prend une liste de mots et génère un ensemble de shingles pour
     * chaque mot en utilisant la fonction createShingles. Elle fusionne les shingles
     * de tous les mots dans un tableau unique et élimine les doublons.
     *
     * @param string[] $wordsList Liste des mots pour lesquels générer des shingles.
     * @return array Un tableau de tous les shingles uniques provenant de la liste des mots.
     */
    private function getAllShinglesFromList(array $wordsList): array {
        $allShingles = [];
        foreach ($wordsList as $word) {
            $wordShingles = $this->createShingles($word);
            $allShingles = array_merge($allShingles, $wordShingles);
        }
        return array_unique($allShingles); // Élimine les doublons
    }

    /**
     * Trie une liste de produits en fonction de leur pertinence par rapport à une liste de mots-clés.
     *
     * Cette fonction calcule un score de pertinence pour chaque produit basé sur la présence et la fréquence
     * des mots-clés dans le titre et la description du produit. Les produits sont ensuite triés par ce score
     * de pertinence en ordre décroissant.
     *
     * @param Produit[] $listeDeProduits Tableau d'objets produits à trier. Chaque produit doit avoir des propriétés 'title' et 'description'.
     * @param string[] $motsCles Tableau de chaînes de caractères représentant les mots-clés pour le calcul de la pertinence.
     * @param string[] $shingles Tableau des shingles des mots clés.
     *
     * @return array Tableau de produits triés par pertinence décroissante. Les produits avec les scores de pertinence les plus élevés sont placés en premier.
     */
    private function trierProduitsParPertinence(array $listeDeProduits, array $motsCles, array $shingles): array {
        $scores = [];

        foreach ($listeDeProduits as $index => $produit) {
            $score = 0;
            foreach (array_merge($motsCles, $shingles) as $mot) {
                $title = iconv('UTF-8', 'ASCII//TRANSLIT', $produit->getTitle());
                $description = iconv('UTF-8', 'ASCII//TRANSLIT', $produit->getDescription());

                if (stripos($title, $mot) !== false) {
                    $score += in_array($mot, $motsCles) ? 10 : 2;
                }
                if (stripos($description, $mot) !== false) {
                    $score += in_array($mot, $motsCles) ? 5 : 1;
                }
            }
            $scores[$index] = $score;
        }

        uasort($scores, fn($a, $b) => $b <=> $a);

        // Réordonner les produits en fonction des scores triés
        $produitsTries = [];
        foreach (array_keys($scores) as $index) {
            $produitsTries[] = $listeDeProduits[$index];
        }

        return $produitsTries;
    }
}
