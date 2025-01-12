<?php

use PHPUnit\Framework\TestCase;
use models\Scrapper;
use Mockery\Mock;

class ScrapperTest extends TestCase
{
    private $scrapperMock;
    private $htmlMock;

    protected function setUp(): void
    {
        $this->scrapperMock = Mockery::mock(Scrapper::class)->makePartial();

        $this->htmlMock = "
        <html>
         <body>
          <article>
           <h2>Title</h2>
           <div class='post-content entry-content'>Content</div>
           <a href='https://example.com'>Link</a><img src='image.jpg'/>
           <footer class='meta'>Author</footer>
          </article>
         </body>
        </html>";

        $this->scrapperMock->shouldReceive('getHtml')->andReturn($this->htmlMock);
    }

    public function testGetArticles()
    {
        $articles = $this->scrapperMock->getArticles();

        $this->assertInstanceOf(\DOMNodeList::class, $articles);
    }

    public function testGetArticle()
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->htmlMock);
        $article = $dom->getElementsByTagName('article')->item(0);

        $articleDetails = $this->scrapperMock->getArticle($article);

        $this->assertEquals('Title', $articleDetails['title']);
        $this->assertEquals('Content', $articleDetails['content']);
        $this->assertEquals('https://example.com', $articleDetails['link']);
        $this->assertEquals('image.jpg', $articleDetails['image']);
        $this->assertEquals('Author', $articleDetails['footer']);
    }

    public function testPrintWebsite()
    {
        ob_start();
        $this->scrapperMock->printWebsite();
        $output = ob_get_clean();

        $this->assertStringContainsString('<h3>Title</h3>', $output);
        $this->assertStringContainsString('<p>Content</p>', $output);
        $this->assertStringContainsString('<img src="image.jpg" height=190em width=100%>', $output);
        $this->assertStringContainsString('<footer> <p><small>PubliéAuthor</small></p> </footer>', $output);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}