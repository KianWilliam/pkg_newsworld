<?php

namespace Kwp121\Component\Newsworld\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Kwp121\Component\Newsworld\Administrator\Service\HTML\AdministratorService;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Fields\FieldsServiceInterface;
use Joomla\Database\DatabaseAwareTrait;
    
class NewsworldComponent extends MVCComponent implements 
    CategoryServiceInterface, RouterServiceInterface, BootableExtensionInterface, AssociationServiceInterface, FieldsServiceInterface
{
	use CategoryServiceTrait;
    use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
    use AssociationServiceTrait;
    use DatabaseAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * We use this to register the helper file class which contains the html for displaying associations
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('newsworldadministrator', new AdministratorService);
	}


	/**
	 * Returns the table name for the count items function for the given section of the category table
	 *
	 */
	protected function getTableNameForSection(string $section = null)
	{
		return 'newsworld';
	}
    
    /**
	 * Returns the name of the published state column in the table
     * for use by the count items function
	 *
	 */
    protected function getStateColumnForSection(string $section = null)
    {
        return 'published';
    }
    
    /**
	 * This is used by com_fields in the admin menu
     * It uses it to create the little Newsworld Items / Categories dropdown
	 *
	 */
    public function getContexts(): array
	{
		Factory::getApplication()->getLanguage()->load('com_newsworld', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_newsworld.newsworld' => Text::_('COM_NEWSWORLD_ITEMS'),
			'com_newsworld.categories' => Text::_('JCATEGORY')
		);

		return $contexts;
	}
	
    /**
	 * This is used by com_fields
     * It indicates to com_fields to use the 'newsworld' context 
     * eg when using the front end form (called 'form').
	 *
	 */
    public function validateSection($section, $item = null)
    {
        if (Factory::getApplication()->isClient('site') && $section == 'form')
        {
            return 'newsworld';
        }
        if ($section != 'newsworld' && $section != 'form')
        {
            return null;
        }

        return $section;
    }
}
