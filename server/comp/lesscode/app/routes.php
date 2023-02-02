<?php

use Phalcon\Mvc\Router;

class LesscodeRoutes
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function handle()
    {
        $apiPrefix = defined('LESSCODE_API_PREFIX') ? LESSCODE_API_PREFIX : 'api';

        $this->router->add(
            "/{$apiPrefix}/lesscode/:controller/:action/:params(|\/)",
            [
                "namespace"  => 'Imee\Controller\Lesscode',
                "controller" => 1,
                "action"     => 2,
                "params"     => 3
            ]
        );

        return $this->router;
    }
}

return (new LesscodeRoutes($router))->handle();