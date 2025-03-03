<?php
/**
 * Fichier Scrapper.php
 *
 * Ce fichier contient la classe 'Scrapper', qui est utilisée pour l'extraction
 * de données depuis un site web. La classe permet de scrapper des articles
 * depuis le site 'https://www.informatiquenews.fr/news' et d'en extraire des
 * informations telles que le titre, le contenu, l'image, le lien et l'auteur
 * de chaque article. Elle utilise les fonctionnalités DOM et XPath de PHP
 * pour extraire et traiter le contenu HTML.
 *
 * PHP version 8.3
 *
 * @category Web_Scraping
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Scrapper
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

/**
 * Class Scrapper
 *
 * Classe générique pour l'extraction de données depuis un site web.
 * Cette classe permet de scrapper les
 * articles du site 'https://www.informatiquenews.fr/news'
 * et d'en extraire des informations telles que le titre, le contenu, l'image,
 * le lien et l'auteur de chaque article. Elle utilise les fonctionnalités DOM
 * et XPath de PHP pour extraire et traiter le contenu HTML.
 *
 * @category Web_Scraping
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 1.0.0
 * @link     https://www.example.com/docs/Scrapper Documentation de la classe
 * @since    2025-01-07
 */
class Scrapper
{
    private string $_url;

    /**
     * Classe Scrapper pour extraire des articles depuis un site web.
     *
     * Cette classe permet de récupérer des articles depuis le site web
     * 'https://boutique.ed-diamond.com/3_gnu-linux-magazine'
     * en scrappant son contenu HTML. Elle extrait
     * des informations telles que le titre, le contenu, l'image, le lien et
     * l'auteur de chaque article. Elle utilise la bibliothèque DOM de PHP pour
     * parser le HTML et XPath pour naviguer dans la structure du DOM.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function __construct()
    {
        $this->_url = 'https://boutique.ed-diamond.com/3_gnu-linux-magazine';
    }
    /**
     * Récupère le contenu HTML de la page d'articles.
     *
     * Cette méthode utilise la fonction 'file_get_contents' pour récupérer le code
     * HTML de la page d'articles depuis l'URL spécifiée dans la classe.
     *
     * @return string Le code HTML de la page.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getHtml() : string
    {
        return file_get_contents($this->_url);
    }

    /**
     * Récupère tous les articles présents sur la page.
     *
     * Cette méthode charge le HTML récupéré avec 'getHtml()' et utilise DOMXPath
     * pour naviguer dans le DOM et extraire tous les éléments '<article>' présents
     * sur la page. Ces éléments sont ensuite retournés sous forme d'une liste.
     *
     * @return DOMNodeList Liste des articles trouvés dans la page.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getArticles() : DOMNodeList
    {
        $html = $this->getHtml();
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $query = '//li[contains(@class, "ajax_block_product mb-4 col-6 col-lg-3")]';
        return $xpath->query($query);
    }

    /**
     * Récupère les détails d'un article spécifique.
     *
     * Cette méthode permet d'extraire le titre, le contenu, le lien, l'image et
     * l'auteur de chaque article en utilisant les balises HTML correspondantes dans
     * l'élément '<article>'.
     *
     * @param DOMElement $article L'article à traiter.
     *
     * @return array Détails de l'article sous forme de tableau associatif avec les
     *               clés suivantes : 'title', 'content', 'link', 'image', 'footer'.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getArticle( DOMElement $article) : array
    {
        $images = $article->getElementsByTagName('img');
        foreach ($images as $div) {
            if ($div != null) {
                $classContent = $div->getAttribute('class');
                if ($classContent == 'img-fluid') {
                    $image = $div->getAttribute('src');
                    break;
                } else {
                    $image = "Pas de contenu.";
                }
            } else {
                $image = "Pas de contenu.";
            }
        }

        return [
            'image' => $image,
        ];
    }


    /**
     * Affiche un article aléatoire du site web.
     *
     * Cette méthode sélectionne un article aléatoire parmi ceux récupérés avec la
     * méthode 'getArticles()'. Elle affiche ensuite cet article en HTML avec son
     * titre, son contenu, son image, son lien et un footer contenant l'auteur de
     * l'article.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function printWebsite() : void
    {
        $articles = $this->getArticles();
        $html = '<div>';

        // Récupérer le premier article
        $article = $articles->item(0);

        if ($article) {
            $varArticle = $this->getArticle($article);

            // Vérifiez si une image est disponible
            if (!empty($varArticle['image'])
                && $varArticle['image'] !== 'pas de contenu'
            ) {
                $imageLarge= str_replace(
                    'home', 'large',
                    $varArticle['image']
                );
                $imageContent = @file_get_contents($imageLarge);

                if ($imageContent !== false) {
                    // Encoder l'image en base64
                    $base64Image = 'data:image/jpeg;base64,'
                                   . base64_encode($imageContent);

                    // Générer le HTML
                    $html .= '<div>';
                    $html .= '<a>';
                    $html .= '<img src="' . $base64Image
                          . '" style="height: 73vh; width: auto;">';
                    $html .= '</a>';
                    $html .= '</div>';
                } else {
                    $html .= '';
                }
            } else {
                $html .= '';
            }
        } else {
            $html .= '';
        }

        $html .= '</div>';
        echo $html;
    }

}
