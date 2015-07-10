<?php

require_once __DIR__ . "/ResolverTestCase.php";

class RouteUrlResolverTest extends ResolverTestCase
{

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routeList;

    protected function setUp()
    {
        $path_url_resolver = new \Concrete\Core\Url\Resolver\PathUrlResolver();
        $routes = new \Symfony\Component\Routing\RouteCollection();
        $context = new \Symfony\Component\Routing\RequestContext();
        $generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $context);

        $this->urlResolver = new \Concrete\Core\Url\Resolver\RouteUrlResolver($path_url_resolver, $generator, $routes);
        $this->routeList = $routes;
    }

    /**
     * Make sure that we can actually resolve a basic route
     */
    public function testRoute()
    {
        $path = '/named/route/path';
        $name = 'named_route';
        $route = new \Concrete\Core\Routing\Route($path);
        $route->setPath($path);

        $this->routeList->add($name, $route);
        $url = $this->canonicalUrlWithPath($path);

        $this->assertEquals((string)$url, (string)$this->urlResolver->resolve(array("route/{$name}")));
    }

    /**
     * Test routes that have inline parameters
     */
    public function testRouteWithParameters()
    {
        $path = '/named/{parameter}/route';
        $name = 'named_route';
        $value = uniqid();

        $route = new \Concrete\Core\Routing\Route($path);
        $route->setPath($path);

        $this->routeList->add($name, $route);
        $url = $this->canonicalUrlWithPath(str_replace("{parameter}", $value, $path));

        $this->assertEquals((string)$url, (string)$this->urlResolver->resolve(array("route/{$name}", array(
            'parameter' => $value
        ))));
    }

    /**
     * Test not finding a named route in the list
     */
    public function testRouteMiss()
    {
        $resolved = uniqid();
        $this->assertEquals($this->urlResolver->resolve(array('route/miss'), $resolved), $resolved);
    }

    /**
     * Test not matching the expected syntax
     */
    public function testNoMatch()
    {
        $resolved = uniqid();
        $this->assertEquals($this->urlResolver->resolve(array('no match'), $resolved), $resolved);
    }

}