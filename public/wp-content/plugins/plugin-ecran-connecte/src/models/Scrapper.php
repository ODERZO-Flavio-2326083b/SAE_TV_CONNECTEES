<?php

namespace models;

class Scrapper
{
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
        $articles = $this->getArticles();
        $html = '<div>';

        // Récupérer le premier article
        $article = $articles->item(0);

        if ($article) {
            $varArticle = $this->getArticle($article);

            // Vérifiez si une image est disponible
            if (!empty($varArticle['image']) && $varArticle['image'] !== 'pas de contenu') {
                $imageContent = @file_get_contents($varArticle['image']);

                if ($imageContent !== false) {
                    // Encoder l'image en base64
                    $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageContent);

                    // Générer le HTML
                    $html .= '<div>';
                    $html .= '<a>';
                    $html .= '<img src="' . $base64Image . '" style="width:100%; height:auto;">';
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