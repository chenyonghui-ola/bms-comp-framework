<?php

use Imee\Libs\Fixed\Loader;

class LesscodeLoader
{
    /**
     * @var Loader
     */
    private $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function handle()
    {
        $namespaces = $this->loader->getNamespaces();

        $namespaceAdd = [
            'Lesscode'                 => 'comp/lesscode/',
            'Imee\Schema'              => 'comp/lesscode/schema/',
            'Imee\Controller\Lesscode' => 'comp/lesscode/app/controller/',
            'Imee\Service\Lesscode'    => 'comp/lesscode/service/',
            'Imee\Models\Cms\Lesscode' => 'comp/lesscode/models/',
        ];

        foreach ($namespaceAdd as &$item)
        {
            if (PHP_SAPI == 'cli') {
                $item = ROOT . DS . $item;
            }
        }

        $namespaces = array_merge($namespaces, $namespaceAdd);

        return $this->loader->registerNamespaces($namespaces);
    }
}

return (new LesscodeLoader($loader))->handle();