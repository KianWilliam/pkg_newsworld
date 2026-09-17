<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_scheduler
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KWP121\Component\Newsworld\Administrator\Field;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * A list field with all available task routines.
 *
 * @since  4.1.0
 */
class NewsnumberField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  4.1.0
     */
    protected $type = 'Newsnumber';
	protected $multiple = 'false';
	
	

    /**
     * Method to get field options
     *
     * @return array
     *
     * @since  4.1.0
     * @throws \Exception
     */


	 protected function getInput()
    {
        $data = $this->collectLayoutData();

        $data['multiple'] = 'false';
        $data['options'] = (array) $this->getOptions();

        return $this->getRenderer($this->layout)->render($data);
    }
	
    protected function getOptions(): array
    {
        $options = parent::getOptions();
		$this->multiple = "false";

        // Get all available task types and sort by title
       	 $nw = Factory::getApplication()->bootComponent('com_newsworld')
            ->getMVCFactory()->createModel('Newsworlds', 'Administrator', ['ignore_request' => true]);
			$news = $nw->getItems();	
			//$newsnumber = count($news);
	   
        //HTMLHelper::_('select.multiple', "false");
        // Closure to add a TaskOption as a <select> option in $options: array
		$options[0]=HTMLHelper::_('select.option', "select one of these:", '');
		$j=1;
			foreach($news as $i=>$n):
			
			if($n->published)          
                $options[$j] = HTMLHelper::_('select.option', $i+1, $i+1);
				$j++;
         
			endforeach;

     

        return $options;
    }
}
