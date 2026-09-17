<?php

namespace Kwp121\Component\Newsworld\Administrator\Table;

//use Joomla\CMS\Table\Nested;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;

\defined('_JEXEC') or die;

/**
 * Newsworld Table class.
 *
 * @since  1.0
 */
class NewsworldTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
    use TaggableTableTrait;
    
     public function __construct(DatabaseDriver $db)
    {
        $this->typeAlias = 'com_newsworld.newsworld';

        parent::__construct('#__newsworld', 'id', $db);
        
        // In functions such as generateTitle() Joomla looks for the 'title' field ...
        $this->setColumnAlias('title', 'greeting');
    }
    
    public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			// Convert the params field to a string.
			$parameter = new Registry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
        
    
   

		return parent::bind($array, $ignore);
	}
    
    public function store($updateNulls = true)
    {
        // add the 'created by' and 'created' date fields if it's a new record
        // and these fields aren't already set
        $date = date('Y-m-d h:i:s');
        $userid = Factory::getApplication()->getIdentity()->get('id');
        if (!$this->id) {
            // new record
            if (empty($this->created_by)) {
                $this->created_by = $userid;
                $this->created    = $date;
            }
        }

        return parent::store();
    }
    
    /**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_newsworld.newsworld.'.(int) $this->$k;
	}
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	2.5
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

    
    public function check()
	{
		$this->alias = trim($this->alias);
		if (empty($this->alias))
		{
			$this->alias = $this->greeting;
		}
		$this->alias = OutputFilter::stringURLSafe($this->alias);
		return true;
	}
    
    public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
    
    /**
     * typeAlias is the key used to find the content_types record
     * needed for creating the history record, the property typeAlias is  set in the constructor
     */
    public function getTypeAlias()
    {
        return $this->typeAlias;
    }
}
