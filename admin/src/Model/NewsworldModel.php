<?php

namespace Kwp121\Component\Newsworld\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Form\Form;
use Joomla\Component\Categories\Administrator\Helper\CategoriesHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\UCM\UCMType;

class NewsworldModel extends AdminModel
{
    use VersionableModelTrait;
    
    // JModelAdmin needs to know this for storing the associations 
	protected $associationsContext = 'com_newsworld.item';
    
    // Contenthistory needs to know this for restoring previous versions
	public $typeAlias = 'com_newsworld.newsworld';
    
    // batch processes supported by newsworld (over and above the standard batch processes)
	protected $newsworld_batch_commands = array(
		'position' => 'batchPosition',
		);

	/**
	 * Method overriding batch in JModelAdmin so that we can include the additional batch processes
	 * which the newsworld component supports.
	 */
	public function batch($commands, $pks, $contexts)
	{
		$this->batch_commands = array_merge($this->batch_commands, $this->newsworld_batch_commands);
		return parent::batch($commands, $pks, $contexts);
	}
	
	/**
	 * Method implementing the batch setting of lat/long values
	 */
	protected function batchPosition($value, $pks, $contexts)
	{
		$app = Factory::getApplication();

		if (isset($value['setposition']) && ($value['setposition'] === 'changePosition'))
		{
			if (empty($this->batchSet))
			{
				// Set some needed variables.
				$this->user = $app->getIdentity();
				$this->table = $this->getTable();
				$this->tableClassName = get_class($this->table);
				$this->contentType = new UCMType;
				$this->type = $this->contentType->getTypeByTable($this->tableClassName);
			}

			foreach ($pks as $pk)
			{
				if ($this->user->authorise('core.edit', $contexts[$pk]))
				{
					$this->table->reset();
					$this->table->load($pk);
					
					if (!$this->table->store())
					{
						$this->setError($this->table->getError());

						return false;
					}
				}
				else
				{
					$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

					return false;
				}
			}
		}
		return true;
	}
    /**
	 * Method to override getItem to allow us to convert the JSON-encoded image information
	 * in the database record into an array for subsequent prefilling of the edit form
     * We also use this method to prefill the associations
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		if ($item AND property_exists($item, 'image'))
		{
			$registry = new Registry($item->image);
			$item->imageinfo = $registry->toArray();
		}
        
        if (!empty($item->id))
		{
			$tagsHelper = new TagsHelper;
			$item->tags = $tagsHelper->getTagIds($item->id, 'com_newsworld.newsworld');
		}
        
        // Load associated items
		if (Associations::isEnabled())
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = Associations::getAssociations('com_newsworld', '#__newsworld', 'com_newsworld.item', (int)$item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}
		return $item; 
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_newsworld.newsworld',
			'newsworld',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

    /**
	 * Method to preprocess the form to add the association fields dynamically
	 *
	 * @return     none
	 */
	protected function preprocessForm(Form $form, $data, $group = 'newsworld')
	{
		// Association content items
		if (Associations::isEnabled())
		{
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1)
			{
				$addform = new \SimpleXMLElement('<form />');
				$fields = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');
				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');
                $fieldset->addAttribute('addfieldprefix', 'Kwp121\Component\Newsworld\Administrator\Field');

				foreach ($languages as $language)
				{
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $language->lang_code);
					$field->addAttribute('type', 'modal_newsworld');
					$field->addAttribute('language', $language->lang_code);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
				}

				$form->load($addform, false);
			}
		}
		parent::preprocessForm($form, $data, $group);
	}
    
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_newsworld.edit.newsworld.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
    
    /**
	 * Method to override the JModelAdmin save() function to handle Save as Copy correctly
	 *
	 * @param   The newsworld record data submitted from the form.
	 *
	 * @return  parent::save() return value
	 */
	/*public function save($data)
	{
		$input = Factory::getApplication()->input;

		// Validate the category id
		// validateCategoryId() returns 0 if the catid can't be found
		if ((int) $data['catid'] > 0)
		{
			$data['catid'] = CategoriesHelper::validateCategoryId($data['catid'], 'com_newsworld');
		}

		// Alter the greeting and alias for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

	
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
		
			// standard Joomla practice is to set the new record as unpublished
			$data['published'] = 0;
		}
        
        $result = parent::save($data);
		if ($result)
		{
			//$this->getTable('', 'Administrator')->rebuild(1);
		}

		return $result;
	}*/
    
    /**
	 * Method to check if it's OK to delete a message. Overrides JModelAdmin::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return Factory::getApplication()->getIdentity()->authorise( "core.delete", "com_newsworld.newsworld." . $record->id );
		}
	}
    
    /**
	 * Prepare a newsworld record for saving in the database
	 */
	protected function prepareTable($table)
	{
	}
    
    /**
	 * Save the record reordering after a record is dragged to a new position in the newsworlds view
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}
    
    protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_newsworld');
	}
}