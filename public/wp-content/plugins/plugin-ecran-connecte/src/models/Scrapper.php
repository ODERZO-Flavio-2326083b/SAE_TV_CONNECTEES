<?php

namespace Models;

class Scrapper
{
    public function __construct()
    {
        $this->url = 'https://www.informatiquenews.fr/news';
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
        $articles = $xpath->query('//article');
        return $articles;
    }

    public function getArticle($article)
    {
        $title = $article->getElementsByTagName('h2')->item(0)->nodeValue;
        $divs = $article->getElementsByTagName('div');
        foreach ($divs as $div) {
            if($div != null) {
                $classContent = $div->getAttribute('class');
                if ($classContent == 'post-content entry-content') {
                    $content = $div->nodeValue;
                    break;
                } else {
                    $content = "pas de contenue";
                }
            }
            else{
                $content = "pas de contenue";
            }
        }
        $link = $article->getElementsByTagName('a')->item(0)->getAttribute('href');
        $image = $article->getElementsByTagName('img')->item(0)->getAttribute('src');

        return [
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'image' => $image
        ];
    }

    public function printWebsite()  {
        $articles = $this->getArticles();

        $html = '<div>';
            $article = $articles->item(rand(0, $articles->length - 1));
            $varArticle = $this->getArticle($article);
            $html .= '<div>';
        //$html .= '<h2>' . $varArticle['title'] . '</h2>';
            $html .= '<p>' . $varArticle['content'] . '</p>';
           // $html .= '<a href="' . $varArticle['link'] . '">';
            $html .= '<img src="' . $varArticle['image'] . '">';
            $html .= '</a>';
            $html .= '</div>';

        $html .= '</div>';
        echo $html;


    }



}