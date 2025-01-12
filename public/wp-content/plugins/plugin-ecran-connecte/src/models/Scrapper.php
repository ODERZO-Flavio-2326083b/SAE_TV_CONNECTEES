<?php

namespace models;

/**
 * Class Scrapper
 *
 * Classe générique pour l'extraction de données depuis un site web.
 * Permet de scrapper les articles du site 'https://www.informatiquenews.fr/news' et d'en extraire des informations telles que
 * le titre, le contenu, l'image, le lien et l'auteur de chaque article. Utilise les fonctionnalités DOM et XPath de PHP pour
 * extraire et traiter le contenu HTML.
 *
 * @package models
 */
class Scrapper
{
    private $url;

    /**
     * Classe Scrapper pour extraire des articles depuis un site web.
     *
     * Cette classe permet de récupérer des articles depuis le site web 'https://www.informatiquenews.fr/news' en scrappant
     * son contenu HTML. Elle extrait des informations telles que le titre, le contenu, l'image, le lien et l'auteur de chaque
     * article. Elle utilise la bibliothèque DOM de PHP pour parser le HTML et XPath pour naviguer dans la structure du DOM.
     *
     * @version 1.0
     * @date 07-01-2025
     */
    public function __construct()
    {
        $this->url = 'https://boutique.ed-diamond.com/3_gnu-linux-magazine';
    }

    public function getHtml()
    {
        $html = file_get_contents($this->url);
        return $html;
    }

    public function getArticles()
    {
        $html = $this->getHtml();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $query = '//li[contains(@class, "ajax_block_product mb-4 col-6 col-lg-3")]';
        $articles = $xpath->query($query);
        return $articles;
    }

    /**
     * Récupère les détails d'un article spécifique.
     *
     * Cette méthode permet d'extraire le titre, le contenu, le lien, l'image et l'auteur de chaque article en utilisant
     * les balises HTML correspondantes dans l'élément `<article>`.
     *
     * @param \DOMElement $article L'article à traiter.
     *
     * @return array Détails de l'article sous forme de tableau associatif avec les clés suivantes : 'title', 'content',
     *               'link', 'image', 'footer'.
     *
     *
     * @version 1.0
     * @date 07-01-2025
     */
    public function getArticle($article)
    {
        $images = $article->getElementsByTagName('img');
        foreach ($images as $div) {
            if($div != null) {
                $classContent = $div->getAttribute('class');
                if ($classContent == 'img-fluid') {
                    $image = $div->getAttribute('src');
                    break;
                } else {
                    $image = "pas de contenue";
                }
            }
            else{
                $image = "pas de contenue";
            }
        }

        return [
            'image' => $image,
        ];
    }

    public function printWebsite()
    {
    /**
     * Affiche un article aléatoire du site web.
     *
     * Cette méthode sélectionne un article aléatoire parmi ceux récupérés avec la méthode `getArticles()`. Elle affiche ensuite
     * cet article en HTML avec son titre, son contenu, son image, son lien et un footer contenant l'auteur de l'article.
     *
     *
     * @version 1.0
     * @date 07-01-2025
     */
    public function printWebsite()  {
        $articles = $this->getArticles();
        $html = '<div>';

        // Récupérer le premier article
        $article = $articles->item(0);

        if ($article) {
            $varArticle = $this->getArticle($article);

            // Vérifiez si une image est disponible
            if (!empty($varArticle['image']) && $varArticle['image'] !== 'pas de contenu') {
                $imageLarge= str_replace('home','large',$varArticle['image']);
                $imageContent = @file_get_contents($imageLarge);

                if ($imageContent !== false) {
                    // Encoder l'image en base64
                    $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageContent);

                    // Générer le HTML
                    $html .= '<div>';
                    $html .= '<a>';
                    $html .= '<img src="' . $base64Image . '" style="height: 73vh; width: auto;">';
                    $html .= '</a>';
                    $html .= '</div>';
                } else {
                    $html .= '<p>Impossible de charger l\'image.</p>';
                }
            } else {
                $html .= '<p>Aucune image trouvée pour cet article.</p>';
            }
        } else {
            $html .= '<p>Aucun article trouvé.</p>';
        }

        $html .= '</div>';
        echo $html;
    }




}