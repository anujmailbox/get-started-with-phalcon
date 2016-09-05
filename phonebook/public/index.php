<?php 

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Config\Adapter\Php as ConfigPhp;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

try {

	$config = new ConfigPhp("../app/config/config.php");	

	// Register the loader component
	$loader = new Loader();
	$loader->registerDirs(array(
		$config->app->controllersDir,
		$config->app->modelsDir
	))->register();

	// DI Container
	$di = new FactoryDefault();

	// Contect with db
	$di->set('db', function () use ($config) {
		return new PdoMysql($config->database->toArray());
	});

	// Register the view service
	$di->set('view', function () use ($config) {
		$view = new View();
		$view->setViewsDir($config->app->viewsDir);
		return $view;
	});
	
	// Register the URL service
	$di['url'] = function () use ($config) {
		$url = new UrlProvider();
		$url->setBaseUri($config->app->baseUri);
		return $url;
	};

	// Setup the default route
    $di->set('router', function () use ($config) {
        $router = new Router();
        $router->setDefaultController($config->app->setDefaultController);
        return $router;
    });
    
	// handle requests
	$app = new Application($di);
	echo $app->handle()->getContent();

} catch (\Exception $e) {
	echo "Exception: ", $e->getMessage();
}