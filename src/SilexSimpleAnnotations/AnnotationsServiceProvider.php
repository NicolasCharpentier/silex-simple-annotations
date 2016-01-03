<?php

namespace  SilexSimpleAnnotations;

use SilexSimpleAnnotations\Parser;
use Silex\Controller;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\Response;


class AnnotationsServiceProvider implements ServiceProviderInterface, ControllerProviderInterface {

    private $controllers = null;
    private $Parser = null;

    // Because i will change this
    private $providerKey  = 'simpleAnnots';
    private $providerName = 'Silex simple annotations';

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app[$this->providerKey . '.recursiv'] = false;

        return $this;
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        if (! isset($app[$this->providerKey . '.controllersPath'])) {
            throw new \Exception($this->providerName . ': Please configure ' . $this->providerKey . '.controllersPath');
        }

        $this->Parser       = new Parser(
            $app[$this->providerKey . '.controllersPath'],
            $app[$this->providerKey . '.recursiv']
        );

        $this->controllers  = $this->Parser->parseEmAll()->getParsedControllers();

        $app->mount('/', $this);
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $routesFactory = $app['controllers_factory'];

        if ($this->controllers === null)
            return $routesFactory;

        foreach ($this->controllers as $controller) {
            Rules::connectController($routesFactory, $controller);
        }

        return $routesFactory;
    }

}