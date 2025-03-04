<?php
/**
 * Fichier Scraper.php
 *
 * Ce fichier contient la classe 'Scraper', qui est responsable de la récupération,
 * de l'analyse et de l'extraction de contenu à partir d'une page web. Cette classe
 * utilise les outils PHP DOM pour extraire des articles, des images et des liens à
 * partir d'une URL spécifiée.
 *
 * La classe permet de gérer les erreurs, de récupérer le contenu HTML d'une page,
 * de sélectionner des articles et d'extraire des informations spécifiques
 * en fonction
 * des sélecteurs CSS fournis.
 *
 * PHP version 8.3
 *
 * @category Scraping
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://www.example.com/docs/Scraper Documentation de la classe
 * @since    2025-03-04
 */

namespace models;

use DOMDocument;
use DOMXPath;
use DOMElement;

/**
 * Class Scraper
 *
 * Cette classe gère le processus de récupération et d'extraction de contenu à partir
 * d'une page web en utilisant le scraping via des sélecteurs définis. Elle permet de
 * récupérer un article, d'extraire des images, des liens, et de gérer
 * les erreurs liées
 * à l'extraction.
 *
 * @category Scraping
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/Scraper Documentation de la classe
 * @since    2025-03-04
 */
class Scraper
{
    private string $_url;
    private string $_articleSelector;
    private array $_infoSelectors;

    /**
     * Constructeur de la classe Scraper
     *
     * Initialise la classe avec l'URL à scraper, le sélecteur CSS pour l'article,
     * et un tableau de sélecteurs CSS pour extraire les informations
     * spécifiques de l'article.
     *
     * @param string $url             L'URL de la page à
     *                                scraper
     * @param string $articleSelector Sélecteur CSS pour
     *                                l'article
     * @param array  $infoSelectors   Tableau des sélecteurs
     *                                CSS pour extraire les informations de
     *                                l'article
     */
    public function __construct(string $url, string $articleSelector,
        array $infoSelectors
    ) {
        $this->_url = $url;
        $this->_articleSelector = $articleSelector;
        $this->_infoSelectors = $infoSelectors;
    }

    /**
     * Récupère le contenu HTML de l'URL spécifiée
     *
     * Cette méthode utilise `file_get_contents` pour
     * récupérer le contenu HTML de l'URL
     * fournie au moment de l'instanciation. Si la récupération échoue, une exception
     * est levée.
     *
     * @return string Le contenu HTML de l'URL
     * @throws \Exception Si le contenu ne peut pas être récupéré
     */
    public function getHtml(): string
    {
        $html = @file_get_contents($this->_url);

        if (!$html) {
            throw new \Exception(
                "Impossible de récupérer le contenu de l'URL: " .
                $this->_url
            );
        }

        return $html;
    }

    /**
     * Crée un objet DOMXPath pour analyser le HTML récupéré
     *
     * Cette méthode récupère le HTML via `getHtml()`, crée un objet DOMDocument,
     * et génère un objet DOMXPath pour faciliter l'analyse du contenu.
     *
     * @return DOMXPath L'objet DOMXPath pour analyser le DOM
     */
    public function getXPath(): DOMXPath
    {
        $html = $this->getHtml();
        $dom = new DOMDocument();

        // Gestion des erreurs pour des HTML mal formés
        @$dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    /**
     * Récupère un article spécifique de la page
     *
     * Cette méthode utilise le sélecteur CSS spécifié pour localiser l'article
     * sur la page et récupérer ses informations à l'aide des autres sélecteurs.
     *
     * @return array|null Un tableau des informations extraites
     * de l'article ou null si aucun article n'est trouvé
     */
    public function getOneArticle(): ?array
    {
        $xpath = $this->getXPath();
        $articles = $xpath->query($this->_articleSelector);

        if ($articles->length > 0) {
            return $this->getArticle($xpath, $articles->item(0));
        }

        return null;
    }

    /**
     * Extrait le premier lien "srcset" d'une image
     *
     * Cette méthode prend un attribut `srcset` d'une balise image et en extrait
     * la première URL de l'image.
     *
     * @param string $srcset L'attribut `srcset` d'une balise image
     *
     * @return string L'URL de la première image
     */
    public function extractFirstSrcSet(string $srcset): string
    {
        $parts = explode(',', $srcset); // Séparer les URLs
        if (count($parts) > 0) {
            // Prendre la première URL
            $firstPart = explode(' ', trim($parts[0]));
            return trim($firstPart[0]); // Nettoyer et retourner l'URL
        }
        return "";
    }

    /**
     * Récupère les informations d'un article en utilisant les sélecteurs définis
     *
     * Cette méthode utilise les sélecteurs CSS pour récupérer les informations
     * définies dans la page web et les retourne sous forme de tableau associatif.
     * Elle gère les types d'éléments comme les images et les liens.
     *
     * @param DOMXPath $xpath   L'objet DOMXPath pour analyser le DOM
     * @param \DOMNode $article L'élément article du DOM
     *
     * @return array Un tableau des informations extraites de l'article
     */
    public function getArticle(DOMXPath $xpath, \DOMNode $article): array
    {
        $details = [];

        foreach ($this->_infoSelectors as $key => $query) {
            $nodes = $xpath->query($query, $article);

            if ($nodes->length > 0) {
                $value = null;

                if ($key === 'image') {
                    $imgNode = $nodes->item(0);
                    if ($imgNode instanceof DOMElement) {
                        $value = null;

                        // Vérifie en priorité srcset, sinon data-src, sinon src
                        if ($imgNode->hasAttribute('srcset')
                            && !empty($imgNode->getAttribute('srcset'))
                        ) {
                            $value = $this->extractFirstSrcSet(
                                $imgNode->getAttribute('srcset')
                            );
                        } elseif ($imgNode->hasAttribute(
                            'data-src'
                        ) && !empty($imgNode->getAttribute('data-src'))
                        ) {
                            $value = $imgNode->getAttribute('data-src');
                        } elseif ($imgNode->hasAttribute('src') 
                            && !empty($imgNode->getAttribute('src'))
                        ) {
                            $value = $imgNode->getAttribute('src');
                        }
                    }
                } elseif ($key === 'link') {
                    $linkNode = $nodes->item(0);
                    $value = $linkNode instanceof DOMElement
                        ? $linkNode->getAttribute('href')
                        : null;
                } else {
                    $value = trim($nodes->item(0)->nodeValue);
                }

                if (!$value) {
                    $value = "Pas de contenu.";
                }

                $details[$key] = $value;
            } else {
                $details[$key] = "Pas de contenu.";
            }
        }

        return $details;
    }

    /**
     * Télécharge et encode l'image en base64
     *
     * Cette méthode prend une URL d'image, la télécharge et l'encode en base64,
     * permettant ainsi d'inclure l'image directement dans le HTML.
     *
     * @param string $imageUrl L'URL de l'image à télécharger
     *
     * @return string|null L'image encodée en base64 ou null si
     * l'image ne peut pas être téléchargée
     */
    public function encodeImageToBase64(string $imageUrl): ?string
    {
        // Vérifier si l'image existe avant de la récupérer
        if (@getimagesize($imageUrl)) {
            $imageContent = @file_get_contents($imageUrl);
            if ($imageContent !== false) {
                return 'data:image/jpeg;base64,' . base64_encode($imageContent);
            }
        }
        return null;
    }

    /**
     * Retourne l'article sous forme HTML
     *
     * Cette méthode génère un bloc HTML contenant les informations extraites
     * de l'article. Si une image est présente, elle est affichée en base64,
     * et les autres informations sont également affichées dans des paragraphes.
     *
     * @return string Un bloc HTML contenant l'article
     */
    public function printWebsite(): string
    {
        $article = $this->getOneArticle();
        if (isset($article['error'])) {
            return "<p style='color: red;
 text-align: center; font-size: 18px;'>{$article['error']}</p>";
        }

        $html = '<div style="font-family: Arial,
 sans-serif; max-width: 800px; margin: auto; text-align: center; padding:
  20px; border-radius: 10px;
   box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">';

        if (isset($article['link'])) {
            $parsedUrl = parse_url($article['link'], PHP_URL_HOST);
            $html .= "<p style='font-size: 14px; color: #666;
 margin-bottom: 10px;'>{$parsedUrl}</p>";
        }

        if ($article) {
            unset($article['author']);

            $hasImage = isset($article['image']) &&
                $article['image'] !== "Pas de contenu.";
            $hasOtherContent = count(
                array_filter(
                    $article,
                    fn($v, $k) => $k !== 'image' &&
                    $v !== "Pas de contenu.", ARRAY_FILTER_USE_BOTH
                )
            ) > 0;

            foreach ($article as $key => $value) {
                if ($value !== "Pas de contenu.") {
                    if ($key === 'image') {
                        if ($imageBase64 = $this->encodeImageToBase64($value)) {
                            $imageStyle = $hasOtherContent ? "max-width: 100%;
                             height: auto; border-radius: 10px; margin-bottom: 15px;"
                                : "width: 50vw; height: 70vh; object-fit: cover; 
                                border-radius: 10px;";
                            $html .= "<img src='{$imageBase64}' style='
{$imageStyle}'>";
                        } else {
                            $html .= "<p style='color: red;'>Image introuvable.</p>";
                        }
                    } elseif ($key === 'link') {
                        $html .= "<p><a href='{$value}'
 target='_blank' style='color: #007BFF; text-decoration: none;
  font-size: 18px;'>{$value}</a></p>";
                    } else {
                        $html .= "<p style='font-size:
 18px; color: #333;'>{$value}</p>";
                    }
                }
            }
        } else {
            $html .= '<p style="color:red;
 font-size: 18px;">Aucun article trouvé.</p>';
        }

        $html .= '</div>';
        return $html;
    }

}

// caca
