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
    
    // not documented. Will inject your controllers as service into the app, and they'll be instanciated with Application $app
    private $controllersAsApplicationAwareService = false;

    const PROVIDER_KEY  = 'simpleAnnots';
    const PROVIDER_NAME = 'Silex simple annotations';

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app[self::PROVIDER_KEY . '.recursiv'] = false;
        $app[self::PROVIDER_KEY . '.controllersAsApplicationAwareServices'] = false;

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
        if (! isset($app[self::PROVIDER_KEY . '.controllersPath'])) {
            throw new \Exception(self::PROVIDER_NAME . ': Please configure ' . self::PROVIDER_KEY . '.controllersPath');
        }

        $this->Parser       = new Parser(
            $app[self::PROVIDER_KEY . '.controllersPath'],
            $app[self::PROVIDER_KEY . '.recursiv']
        );
        
        $this->controllersAsApplicationAwareService = $app[self::PROVIDER_KEY . '.controllersAsApplicationAwareServices'];

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

        if (0 == count($this->controllers)) {
            return $routesFactory;   
        }

        foreach ($this->controllers as $controller) {
            if ($this->controllersAsApplicationAwareService) {
                $controller['Access'] = 'controller.' . $controller['Namespace'] . '.' . $controller['Name'];

                $controllerClass = $controller['Namespace'] . '\\' . $controller['Name'];

                $app[$controller['Access']] = $app->share(function () use ($app, $controllerClass) {
                      return new $controllerClass($app);
                });
            } else {
                $controller['Access'] = $controller['Namespace'] . '\\' . $controller['Name'] . ':';
            }
            
            Rules::connectController($routesFactory, $controller);
        }

        return $routesFactory;
    }

}