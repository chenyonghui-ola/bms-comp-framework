<?php

use Imee\Libs\OutputError;
use Imee\Libs\ErrorHandler;
use Imee\Exception\ApiException;
use Imee\Service\Event\ApplicationEventListener;
use Imee\Service\Event\DispatchEventListener;
use Phalcon\Mvc\Application;

class ImeeApplication
{
    public function __construct()
    {
    }

    private static $_instance = null;

    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new ImeeApplication();
        }
        return self::$_instance;
    }

    public function run()
    {
        $this->init();
    }

    private function init()
    {
        try {
            $loader = require_once __DIR__ . '/loader.php';
            $loader->register();

            // Create a DI
            $di = require_once __DIR__ . '/di.php';

            // Handle the request
            $application = new Application($di);
            ErrorHandler::register();
            $application->setEventsManager($eventsManager);

            $eventsManager->attach('application', new ApplicationEventListener());
            $eventsManager->attach('dispatch', new DispatchEventListener());

            echo $application->handle()->getContent();
        } catch (ApiException $e) {
            $array = array(
                'success' => false,
                'msg'     => $e->getMsg(),
                'code'    => $e->getCode()
            );
            echo json_encode($array, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            ob_end_clean();
            new OutputError($e);
        }
    }
}
