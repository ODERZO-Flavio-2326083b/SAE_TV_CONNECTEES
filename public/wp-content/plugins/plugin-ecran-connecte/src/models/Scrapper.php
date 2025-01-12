<?php
// TODO : Ajouter la doc du fichier
namespace models;

/**
 * TODO : Ajouter les tags @author, @category, @license et @link
 * Class Scrapper
 *
 * Classe générique pour l'extraction de données depuis un site web.
 * Permet de scrapper les articles du site 'https://www.informatiquenews.fr/news' et
 * d'en extraire des informations telles que le titre, le contenu, l'image, le lien
 * et l'auteur de chaque article. Utilise les fonctionnalités DOM et XPath de PHP
 * pour extraire et traiter le contenu HTML.
 *
 * @package models
 */
class Scrapper
{
    /**
     * Classe Scrapper pour extraire des articles depuis un site web.
     *
     * Cette classe permet de récupérer des articles depuis le site web
     * 'https://www.informatiquenews.fr/news' en scrappant son contenu HTML. Elle
     * extrait des informations telles que le titre, le contenu, l'image, le lien et
     * l'auteur de chaque article. Elle utilise la bibliothèque DOM de PHP pour
     * parser le HTML et XPath pour naviguer dans la structure du DOM.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function __construct()
    {
        $this->url = 'https://www.informatiquenews.fr/news';
    }

    /**
     * Récupère le contenu HTML de la page d'articles.
     *
     * Cette méthode utilise la fonction `file_get_contents` pour récupérer le code
     * HTML de la page d'articles depuis l'URL spécifiée dans la classe.
     *
     * @return string Le code HTML de la page.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getHtml()
    {
        $html = file_get_contents($this->url);
        return $html;
    }

    /**
     * Récupère tous les articles présents sur la page.
     *
     * Cette méthode charge le HTML récupéré avec `getHtml()` et utilise DOMXPath
     * pour naviguer dans le DOM et extraire tous les éléments `<article>` présents
     * sur la page. Ces éléments sont ensuite retournés sous forme d'une liste.
     *
     * @return \DOMNodeList Liste des articles trouvés dans la page.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getArticles()
    {
        $html = $this->getHtml();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $articles = $xpath->query('//article');
        return $articles;
    }

    /**
     * Récupère les détails d'un article spécifique.
     *
     * Cette méthode permet d'extraire le titre, le contenu, le lien, l'image et
     * l'auteur de chaque article en utilisant les balises HTML correspondantes dans
     * l'élément `<article>`.
     *
     * @param \DOMElement $article L'article à traiter.
     *
     * @return array Détails de l'article sous forme de tableau associatif avec les
     *               clés suivantes : 'title', 'content', 'link', 'image', 'footer'.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getArticle($article)
    {
        $title = $article->getElementsByTagName('h2')->item(0)->nodeValue;
        $divs = $article->getElementsByTagName('div');
        foreach ($divs as $div) {
            if ($div != null) {
                $classContent = $div->getAttribute('class');
                if ($classContent == 'post-content entry-content') {
                    $content = $div->nodeValue;
                    break;
                } else {
                    $content = "pas de contenu";
                }
            } else {
                $content = "pas de contenu";
            }
        }
        $link = $article->getElementsByTagName('a')->item(0)->getAttribute('href');

        $image = $article->getElementsByTagName('img')->item(
            $article->getElementsByTagName('img')->length-1
        )->getAttribute('src');

        $footers = $article->getElementsByTagName('footer');
        foreach ($footers as $footer) {
            if ($footer != null) {
                $classContent = $footer->getAttribute('class');
                if ($classContent == 'meta') {
                    $author = $footer->nodeValue;
                    break;
                } else {
                    $author = "pas de contenue";
                }
            } else {
                $author = "pas de contenue";
            }
        }

        return [
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'image' => $image,
            'footer'=> $author
        ];
    }

    /**
     * Affiche un article aléatoire du site web.
     *
     * Cette méthode sélectionne un article aléatoire parmi ceux récupérés avec la
     * méthode `getArticles()`. Elle affiche ensuite cet article en HTML avec son
     * titre, son contenu, son image, son lien et un footer contenant l'auteur de
     * l'article.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function printWebsite()
    {
        $articles = $this->getArticles();

        $html = '<div>';
            $article = $articles->item(rand(0, $articles->length - 1));
            $varArticle = $this->getArticle($article);
            $html .= '<div>';
            $html .= '<h3>' . $varArticle['title'] . '</h3>';
            $html .= '<p>' . $varArticle['content'] . '</p>';
            $html .= '<a href="' . $varArticle['link'] . '">';
            $html .= '<img src="'
                . $varArticle['image'] . '" height=190em width=100%>';
            $html .= '</a>';
            $html .= '<footer> <p><small>Publié' . $varArticle['footer']
                . '</small></p> </footer>';
            $html .= '</div>';

        $html .= '</div>';
        echo $html;


    }



}
