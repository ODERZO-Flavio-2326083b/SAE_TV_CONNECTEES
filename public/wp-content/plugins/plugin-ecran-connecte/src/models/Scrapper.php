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

        $image = $article->getElementsByTagName('img')->item($article->getElementsByTagName('img')->length-1)->getAttribute('src');

        $footers = $article->getElementsByTagName('footer');
        foreach ($footers as $footer) {
            if($footer != null) {
                $classContent = $footer->getAttribute('class');
                if ($classContent == 'meta') {
                    $author = $footer->nodeValue;
                    break;
                } else {
                    $author = "pas de contenue";
                }
            }
            else{
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

    public function printWebsite()  {
        $articles = $this->getArticles();

        $html = '<div>';
            $article = $articles->item(rand(0, $articles->length - 1));
            $varArticle = $this->getArticle($article);
            $html .= '<div>';
            $html .= '<h3>' . $varArticle['title'] . '</h3>';
            $html .= '<p>' . $varArticle['content'] . '</p>';
            $html .= '<a href="' . $varArticle['link'] . '">';
            $html .= '<img src="' . $varArticle['image'] . '" height=190em width=100%>';
            $html .= '</a>';
            $html .= '<footer> <p><small>Publié' . $varArticle['footer'] . '</small></p> </footer>';
            $html .= '</div>';

        $html .= '</div>';
        echo $html;


    }



}