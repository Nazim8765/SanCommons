<?php

namespace SanCommons\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;
    
class Myplugin extends AbstractPlugin
{
    protected $sesscontainer ;

    private function getSessContainer()
    {
        if (!$this->sesscontainer) {
            $this->sesscontainer = new SessionContainer('zftutorial');
        }
        return $this->sesscontainer;
    }
    
    public function doAuthorization($e)
    {
        //setting ACL...
        $acl = new Acl();
        //add role ..
        $acl->addRole(new Role('anonymous'));
        $acl->addRole(new Role('user'),  'anonymous');
        $acl->addRole(new Role('admin'), 'user');
        
        $acl->addResource(new Resource('Application'));
        $acl->addResource(new Resource('Login'));
        
        $acl->deny('anonymous', 'Application', 'view');
        $acl->allow('anonymous', 'Login', 'view');
        
        $acl->allow('user',
            array('Application'),
            array('view')
        );
        
        //admin is child of user, can publish, edit, and view too !
        $acl->allow('admin',
            array('Application'),
            array('publish', 'edit')
        );
        
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $namespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        
        $role = (! $this->getSessContainer()->role ) ? 'anonymous' : $this->getSessContainer()->role;
        if ( ! $acl->isAllowed($role, $namespace, 'view')){
            $router = $e->getRouter();
            $url    = $router->assemble(array(), array('name' => 'Login/auth'));
        
            $response = $e->getResponse();
            $response->setStatusCode(302);
            //redirect to login route...
            $response->getHeaders()->addHeaderLine('Location', $url);            
        }
    }
}
