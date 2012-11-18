<?php

namespace SanCommons;

use Zend\Mvc\MvcEvent,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $eventManager->attach('dispatch', array($this, 'loadConfiguration'), 2);
        //you can attach other function need here...
    }

    public function loadConfiguration(MvcEvent $e)
    {
        $application   = $e->getApplication();
	$sm            = $application->getServiceManager();
	$sharedManager = $application->getEventManager()->getSharedManager();

	$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch', 
             function($e) use ($sm) {
		$sm->get('ControllerPluginManager')->get('Myplugin')
                   ->doAuthorization($e); //pass to the plugin...    
	    }
        );
    }
    public function getAutoloaderConfig(){ /*common code */}
    public function getConfig(){ /* common code */ }
}