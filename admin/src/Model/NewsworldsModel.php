<?php

namespace Kwp121\Component\Newsworld\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Component\ComponentHelper;


class NewsworldsModel extends ListModel
{
    public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'title',
				'created_by',
				'created',
                'language',
				'category_id',
                'access',
                'association',
				'published',
				'version',
				'params',
				'ordering'
            );
		}

		parent::__construct($config);
	}
    
    protected function populateState($ordering = null, $direction = null)
	{
		//$app = Factory::getApplication();
		
			$app = Factory::getApplication('administrator');

		// Load the filter state
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'int');
		$this->setState('filter.published', $published);

		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 20), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		// Load the parameters
		$params = ComponentHelper::getParams('com_newsworld');
		$this->setState('params', $params);

		// List state information
		parent::populateState('a.title', 'asc');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Adjust the context to support forced languages.
		$forcedLanguage = $app->input->get('forcedLanguage', '', 'CMD');
		if ($forcedLanguage)
		{
			$this->context .= '.' . $forcedLanguage;
		}

		parent::populateState($ordering, $direction);
        
		// If there's a forced language then define that filter for the query where clause
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
		}
	}
    
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  A SQL query
	 */
	protected function getListQuery()
	{
        // Initialize variables.
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $user = Factory::getApplication()->getIdentity();

		// Create the base select statement.
        $query->select('a.id as id,  a.title as title, a.published as published, a.created as created, a.access as access,
            a.checked_out as checked_out, a.checked_out_time as checked_out_time, a.catid as catid,
           a.alias as alias, a.language as language,a.version as version, a.params as params, a.ordering as ordering')
			->from($db->quoteName('#__newsworld', 'a'));

        // Join over the categories.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid');

		// Join with users table to get the username of the author
		$query->select($db->quoteName('u.username', 'author'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = a.created_by');
            
        // Join with users table to get the username of the person who checked the record out
	//	$query->select($db->quoteName('u2.username', 'editor'))
	//		->join('LEFT', $db->quoteName('#__users', 'u2') . ' ON u2.id = a.checked_out');
            
        // Join with languages table to get the language title and image to display
		// Put these into fields called language_title and language_image so that 
		// we can use the little com_content layout to display the map symbol
		$query->select($db->quoteName('l.title', 'language_title') . "," .$db->quoteName('l.image', 'language_image'))
			->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON l.lang_code = a.language');

        // Join over the associations - we just want to know if there are any, at this stage
		if (Associations::isEnabled())
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_newsworld.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}
        
        // Join over the access levels, to get the name of the access level
        $query->select('v.title AS access_level')
                ->join('LEFT', '#__viewlevels AS v ON v.id = a.access');
           
        // Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('title LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}
        
        // Filter by language, if the user has set that in the filter field
		$language = $this->getState('filter.language');
		if ($language)
		{
			$query->where('a.language = ' . $db->quote($language));
		}
        
        // Filter by categories
        $catid = $this->getState('filter.category_id');
        if ($catid)
        {
                $query->where("a.catid = " . $db->quote($db->escape($catid)));
        }
        
        // Display only records to which the user has access
        if (!$user->authorise('core.admin'))  // ie if not SuperUser
        {
                $userAccessLevels = implode(',', $user->getAuthorisedViewLevels());
                $query->where('a.access IN (' . $userAccessLevels . ')');
                $query->where('c.access IN (' . $userAccessLevels . ')');
        }

        // exclude root newsworld record
        $query->where('a.id >= 1 ');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'id');
		$orderDirn 	= $this->state->get('list.direction', 'asc');
	//	var_dump($orderCol);

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		//var_dump($query);
	//	exit();

		return $query;
	}
}