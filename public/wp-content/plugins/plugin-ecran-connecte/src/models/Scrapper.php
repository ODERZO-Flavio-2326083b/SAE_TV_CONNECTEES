<?php

namespace models;

use DOMDocument;
use DOMXPath;
use DOMElement;

class Scrapper
{
    private string $_url;
    private string $_articleSelector;
    private array $_infoSelectors;

    public function __construct(string $url, string $articleSelector, array $infoSelectors)
    {
        $this->_url = $url;
        $this->_articleSelector = $articleSelector;
        $this->_infoSelectors = $infoSelectors;
    }

    /**
     * Récupère le HTML de l'URL et gère les erreurs possibles
     */
    public function getHtml(): string
    {
        $html = @file_get_contents($this->_url);

        if (!$html) {
            throw new \Exception("Impossible de récupérer le contenu de l'URL: " . $this->_url);
        }

        return $html;
    }

    /**
     * Crée et renvoie un objet DOMXPath
     */
    private function getXPath(): DOMXPath
    {
        $html = $this->getHtml();
        $dom = new DOMDocument();

        // Gestion des erreurs pour des HTML mal formés
        @$dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    /**
     * Récupère un article spécifique
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
     * Récupère les informations d'un article en utilisant les sélecteurs définis
     */
    private function getArticle(DOMXPath $xpath, \DOMNode $article): array
    {
        $details = [];

        foreach ($this->_infoSelectors as $key => $query) {
            $nodes = $xpath->query($query, $article);

            if ($nodes->length > 0) {
                $value = null;

                if ($key === 'image') {
                    $imgNode = $nodes->item(0);
                    $value = $imgNode instanceof DOMElement
                        ? ($imgNode->getAttribute('src') ?: $imgNode->getAttribute('data-lazy-src'))
                        : null;
                } elseif ($key === 'link') {
                    $linkNode = $nodes->item(0);
                    $value = $linkNode instanceof DOMElement
                        ? $linkNode->getAttribute('href')
                        : null;
                } else {
                    $value = trim($nodes->item(0)->nodeValue);
                }

                // Si le contenu est vide, afficher "Pas de contenu."
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
     * Affiche l'article dans un format HTML
     */
    public function printWebsite(): void
    {
        $article = $this->getOneArticle();
        $html = '<div>';

        if ($article) {
            $html .= '<div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">';

            foreach ($article as $key => $value) {
                // Ne pas afficher si "Pas de contenu"
                if ($value !== "Pas de contenu.") {
                    if ($key === 'image') {
                        $html .= "<p><strong>{$key} :</strong> <br><img src='{$value}' style='max-width: 300px;'></p>";
                    } elseif ($key === 'link') {
                        $html .= "<p><strong>{$key} :</strong> <a href='{$value}' target='_blank'>{$value}</a></p>";
                    } else {
                        $html .= "<p><strong>{$key} :</strong> {$value}</p>";
                    }
                }
            }

            $html .= '</div>';
        } else {
            $html .= '<p>Aucun article trouvé.</p>';
        }

        $html .= '</div>';
        echo $html;
    }
}
