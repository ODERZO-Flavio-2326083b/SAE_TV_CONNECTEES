<?php

namespace models;

use DOMDocument;
use DOMXPath;

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

    public function getHtml(): string
    {
        return file_get_contents($this->_url);
    }

    private function getXPath(): DOMXPath
    {
        $html = $this->getHtml();
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    public function getOneArticle(): ?array
    {
        $xpath = $this->getXPath();
        $articles = $xpath->query($this->_articleSelector);

        if ($articles->length > 0) {
            return $this->getArticle($xpath, $articles->item(0));
        }

        return null;
    }

    private function getArticle(DOMXPath $xpath, \DOMNode $article): array
    {
        $details = [];

        foreach ($this->_infoSelectors as $key => $query) {
            $nodes = $xpath->query($query, $article);

            if ($nodes->length > 0) {
                if ($key === 'image' || $key === 'link') {
                    // Vérifie si le nœud est bien un élément DOMElement avant d'appeler getAttribute()
                    $value = $nodes->item(0) instanceof DOMElement ? $nodes->item(0)->getAttribute($key === 'image' ? 'src' : 'href') : "Pas de contenu.";
                } else {
                    $value = trim($nodes->item(0)->nodeValue) ?: "Pas de contenu.";
                }
                $details[$key] = $value;
            } else {
                $details[$key] = "Pas de contenu.";
            }
        }

        return $details;
    }


    public function printWebsite(): void
    {
        $article = $this->getOneArticle();
        $html = '<div>';

        if ($article) {
            $html .= '<div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">';

            foreach ($article as $key => $value) {
                if ($key === 'image') {
                    $html .= "<p><strong>{$key} :</strong> <br><img src='{$value}' style='max-width: 300px;'></p>";
                } elseif ($key === 'link') {
                    $html .= "<p><strong>{$key} :</strong> <a href='{$value}' target='_blank'>{$value}</a></p>";
                } else {
                    $html .= "<p><strong>{$key} :</strong> {$value}</p>";
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



