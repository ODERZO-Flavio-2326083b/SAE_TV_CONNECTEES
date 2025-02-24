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
                    if ($imgNode instanceof DOMElement) {
                        // Récupère l'URL de l'image depuis src, data-src ou srcset
                        $value = $imgNode->getAttribute('data-src') ?:
                            $imgNode->getAttribute('src') ?:
                                $this->extractFirstSrcSet($imgNode->getAttribute('srcset'));
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
     */
    private function encodeImageToBase64(string $imageUrl): ?string
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

    private function extractFirstSrcSet(string $srcset): ?string
    {
        if (!$srcset) return null;

        // Le srcset contient plusieurs URLs avec des tailles, on prend la première
        $parts = explode(',', $srcset);
        if (isset($parts[0])) {
            $firstPart = trim($parts[0]);
            $url = explode(' ', $firstPart)[0]; // Prend uniquement l'URL
            return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
        }

        return null;
    }

    /**
     * Affiche l'article dans un format HTML
     */
    public function printWebsite(): void
    {
        $article = $this->getOneArticle();
        var_dump($article); // Vérifie le contenu récupéré
        $html = '<div>';

        if ($article) {
            $html .= '<div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">';

            foreach ($article as $key => $value) {
                if ($value !== "Pas de contenu.") {
                    if ($key === 'image') {
                        $imageBase64 = $this->encodeImageToBase64($value);
                        if ($imageBase64) {
                            $html .= "<p><strong>{$key} :</strong> <br><img src='{$imageBase64}' style='height: 7em; width: auto;'></p>";
                        } else {
                            $html .= "<p>Image introuvable.</p>";
                        }
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
