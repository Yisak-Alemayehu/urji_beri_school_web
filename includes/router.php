<?php
/**
 * Front Controller Router
 * Urji Beri School Website
 */

class Router {
    private array $routes = [];

    public function __construct() {
        $this->routes = [
            ['pattern' => 'api/gallery', 'file' => 'api/gallery.php', 'page' => 'api-gallery'],
            ['pattern' => 'blog/category/{category}', 'file' => 'blog.php', 'page' => 'blog', 'params' => ['category']],
            ['pattern' => 'blog/{slug}', 'file' => 'blog-detail.php', 'page' => 'blog-detail', 'params' => ['slug']],
            ['pattern' => 'gallery/{category}', 'file' => 'gallery.php', 'page' => 'gallery', 'params' => ['category']],
            ['pattern' => 'sitemap.xml', 'file' => 'sitemap.php', 'page' => 'sitemap'],
            ['pattern' => 'about', 'file' => 'about.php', 'page' => 'about'],
            ['pattern' => 'contact', 'file' => 'contact.php', 'page' => 'contact'],
            ['pattern' => 'director', 'file' => 'director.php', 'page' => 'director'],
            ['pattern' => 'gallery', 'file' => 'gallery.php', 'page' => 'gallery'],
            ['pattern' => 'blog', 'file' => 'blog.php', 'page' => 'blog'],
            ['pattern' => '', 'file' => 'home.php', 'page' => 'index'],
        ];
    }

    public function dispatch(string $uri): void {
        $path = trim(parse_url($uri, PHP_URL_PATH) ?? '', '/');
        $path = rawurldecode($path);

        // Normalize index.php in path
        if ($path === 'index.php') {
            $path = '';
        }

        foreach ($this->routes as $route) {
            $params = $this->match($route['pattern'], $path);
            if ($params === null) {
                continue;
            }

            foreach ($params as $key => $value) {
                $_GET[$key] = $value;
                $_REQUEST[$key] = $value;
            }

            if (!defined('CURRENT_PAGE')) {
                define('CURRENT_PAGE', $route['page']);
            }

            if (!defined('ROUTE_PATH')) {
                define('ROUTE_PATH', $path);
            }

            $file = BASE_PATH . '/' . $route['file'];
            if (!is_file($file)) {
                break;
            }

            require $file;
            return;
        }

        http_response_code(404);
        if (!defined('CURRENT_PAGE')) {
            define('CURRENT_PAGE', '404');
        }
        require BASE_PATH . '/404.php';
    }

    private function match(string $pattern, string $path): ?array {
        $pattern = trim($pattern, '/');
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
