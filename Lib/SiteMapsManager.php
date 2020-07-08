<?php

namespace Core;

use Application\Pdo\PDOFactory;

class SiteMapsManager
{
    protected $routes_urls = [];

    public function __construct()
    {
        $xml = new \DOMDocument;
        $xml->load(APP_DIR.'Config/routes.xml');
        $routes = $xml->getElementsByTagName('route');
        foreach ($routes as $route) {
            if ($route->hasAttribute('sitemap') and $route->getAttribute('sitemap') == 1) {
                array_push(
                    $this->routes_urls,
                    [
                        'url' => $route->getAttribute('url'),
                        'action' => $route->getAttribute('action'),
                    ]
                );
            }
        }
    }

    public function createSiteMap()
    {
        $file = APP_DIR.'Public/sitemaps.xml';
        if (file_exists($file)) {
            unlink($file);
        }
        $xml    = new \DOMDocument('1.0', 'utf-8');
        $urlset = $xml->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->appendChild($urlset);
        foreach ($this->getUrls() as $urlDatas) {
            foreach ($urlDatas as $urlData) {

                $url = $xml->createElement('url');
                $loc = $xml->createElement('loc', $urlData['loc']);
                $url->appendChild($loc);

                if ($urlData['lastmod']) {
                    $lastmod = $xml->createElement('lastmod', date('Y-m-d'));
                    $url->appendChild($lastmod);
                }

                $priority = $xml->createElement('priority', $urlData['priority']);
                $url->appendChild($priority);

                $urlset->appendChild($url);
            }

            $xml->preserveWhiteSpace = false;
            $xml->formatOutput       = true;
            $xml->save($file);
        }
    }

    public function getUrls()
    {
        $urls = [
            'pages' => [],
            'categories' => [],
            'posts' => [],
        ];

        $managers    = new Managers('PDO', PDOFactory::getMysqlConnexion());
        $postManager = $managers->getManagerOf('Post');
        $posts       = $postManager->getList();
        $categories  = $postManager->getCategories();

        foreach ($this->routes_urls as $route) {
            array_push($urls['pages'], [
                'name' => ucfirst($route['action']),
                'loc' => SITE_URL.$route['url'],
                'priority' => 0.9,
                'lastmod' => null,
            ]);
        }
        foreach ($categories as $category) {
            array_push($urls['categories'], [
                'name' => $category['category_name'],
                'loc' => SITE_URL.'/post/'.$category['category_slug'].'/',
                'priority' => 0.8,
                'lastmod' => null,
            ]);
        }
        foreach ($posts as $post) {
            array_push($urls['posts'], [
                'name' => $post->getTitle(),
                'loc' => SITE_URL.'/post/'.$post->getCategorySlug().'/'.$post->getSlug().'/',
                'priority' => 0.7,
                'lastmod' => $post->getDateEdit() ?? $post->getDateAdd(),
            ]);
        }

        array_push($urls['pages'], [
            'name' => 'Blog',
            'loc' => SITE_URL.'/post/all/',
            'priority' => 0.7,
            'lastmod' => null,
        ]);

        return $urls;
    }
}
