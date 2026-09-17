<?php

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\HTML\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Kwp121\Component\Newsworld\Administrator\Extension\NewsworldComponent;
use Kwp121\Component\Newsworld\Administrator\Helper\AssociationsHelper;
use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\Database\DatabaseInterface;


return new class implements ServiceProviderInterface {
    
    public function register(Container $container): void 
    {
        $container->set(AssociationExtensionInterface::class, new AssociationsHelper());
        
        $container->registerServiceProvider(new CategoryFactory('\\Kwp121\\Component\\Newsworld'));
        $container->registerServiceProvider(new MVCFactory('\\Kwp121\\Component\\Newsworld'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Kwp121\\Component\\Newsworld'));
        $container->registerServiceProvider(new RouterFactory('\\Kwp121\\Component\\Newsworld'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new NewsworldComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));
                $component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setDatabase($container->get(DatabaseInterface::class));

                return $component;
            }
        );
    }
};