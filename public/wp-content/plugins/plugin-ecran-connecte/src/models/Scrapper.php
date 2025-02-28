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

    private function extractFirstSrcSet(string $srcset): string {
        $parts = explode(',', $srcset); // Séparer les URLs
        if (count($parts) > 0) {
            $firstPart = explode(' ', trim($parts[0])); // Prendre la première URL
            return trim($firstPart[0]); // Nettoyer et retourner l'URL
        }
        return "";
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
                        $value = null;

                        // Vérifie en priorité srcset, sinon data-src, sinon src
                        if ($imgNode->hasAttribute('srcset') && !empty($imgNode->getAttribute('srcset'))) {
                            $value = $this->extractFirstSrcSet($imgNode->getAttribute('srcset'));
                        } elseif ($imgNode->hasAttribute('data-src') && !empty($imgNode->getAttribute('data-src'))) {
                            $value = $imgNode->getAttribute('data-src');
                        } elseif ($imgNode->hasAttribute('src') && !empty($imgNode->getAttribute('src'))) {
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


    /**
     * Affiche l'article dans un format HTML
     */
    public function printWebsite(): void
    {
        $article = $this->getOneArticle();
        if (isset($article['error'])) {
            echo "<p style='color: red; text-align: center; font-size: 18px;'>{$article['error']}</p>";
            return;
        }

        $html = '<div style="font-family: Arial, sans-serif; max-width: 800px; margin: auto; text-align: center; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f9f9f9;">';

        if (isset($article['link'])) {
            $parsedUrl = parse_url($article['link'], PHP_URL_HOST);
            $html .= "<p style='font-size: 14px; color: #666; margin-bottom: 10px;'>{$parsedUrl}</p>";
        }

        if ($article) {
            unset($article['author']);

            $hasImage = isset($article['image']) && $article['image'] !== "Pas de contenu.";
            $hasOtherContent = count(array_filter($article, fn($v, $k) => $k !== 'image' && $v !== "Pas de contenu.", ARRAY_FILTER_USE_BOTH)) > 0;

            foreach ($article as $key => $value) {
                if ($value !== "Pas de contenu.") {
                    if ($key === 'image') {
                        if ($imageBase64 = $this->encodeImageToBase64($value)) {
                            $imageStyle = $hasOtherContent ? "max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 15px;" : "width: 50vw; height: 70vh; object-fit: cover; border-radius: 10px;";
                            $html .= "<img src='{$imageBase64}' style='{$imageStyle}'>";
                        } else {
                            $html .= "<p style='color: red;'>Image introuvable.</p>";
                        }
                    } elseif ($key === 'link') {
                        $html .= "<p><a href='{$value}' target='_blank' style='color: #007BFF; text-decoration: none; font-size: 18px;'>{$value}</a></p>";
                    } else {
                        $html .= "<p style='font-size: 18px; color: #333;'>{$value}</p>";
                    }
                }
            }
        } else {
            $html .= '<p style="color:red; font-size: 18px;">Aucun article trouvé.</p>';
        }

        $html .= '</div>';
        echo $html;
    }



}
