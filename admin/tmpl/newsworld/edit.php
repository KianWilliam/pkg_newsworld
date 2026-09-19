<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted Access');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_contenthistory');

$wa->useScript('keepalive')
    ->useScript('form.validate')
	    ->useScript('com_contenthistory.admin-history-versions');
;

$this->useCoreUI = true;
    
// if &tmpl=component used on first invocation, ensure it's on subsequent ones too
$input = Factory::getApplication()->input;
$tmpl = $input->getCmd('tmpl', '') === 'component' ? '&tmpl=component' : '';

$form  = $this->getForm();
?>

<form action="<?php echo Route::_('index.php?option=com_newsworld&layout=edit' . $tmpl . '&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
    <input id="jform_title" type="hidden" name="newsworld-message-title"/>

    <div class="row title-alias form-vertical mb-3">
        <div class="col-12 col-md-6">
            <?php 
			echo $form->renderField('title');
			?>
        </div>
        <div class="col-12 col-md-6">
            <?php echo $form->renderField('alias'); ?>
        </div>
		
		  <div class="col-12 col-md-6">
            <?php echo $form->renderField('created_by'); ?>
        </div>
		
    </div>
        
    <div class="main-card">

    <?php echo HtmlHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>
    <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'details', 
        empty($this->item->id) ? Text::_('COM_NEWSWORLD_TAB_NEW_MESSAGE') : Text::_('COM_NEWSWORLD_TAB_EDIT_MESSAGE')); ?>
        <fieldset class="adminform">
            <legend><?php echo Text::_('COM_NEWSWORLD_LEGEND_DETAILS') ?></legend>
            <div class="row">
                 <div class="col-12 col-lg-9">
                    <?php echo $this->form->getInput('description');  ?>
                </div>
                <div class="col-12 col-lg-3">
                    <?php echo $this->form->renderFieldset('details');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo HtmlHelper::_('uitab.endTab'); ?>
    
   

    <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'params', Text::_('COM_NEWSWORLD_TAB_PARAMS')); ?>
        <fieldset class="adminform">
            <legend><?php echo Text::_('COM_NEWSWORLD_LEGEND_PARAMS') ?></legend>
            <div class="row">
                <div class="col-lg-6">
                    <?php echo $this->form->renderFieldset('params');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo HtmlHelper::_('uitab.endTab'); ?>
    
    <?php if (Associations::isEnabled()) : ?>
        <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('COM_NEWSWORLD_TAB_ASSOCIATIONS')); ?>
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_NEWSWORLD_LEGEND_ASSOCIATIONS') ?></legend>
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo LayoutHelper::render('joomla.edit.associations', $this);  ?>
                    </div>
                </div>
            </fieldset>
        <?php echo HtmlHelper::_('uitab.endTab'); ?>
    <?php endif; ?>

    <?php echo HtmlHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_NEWSWORLD_TAB_PERMISSIONS')); ?>
        <fieldset class="adminform">
            <legend><?php echo Text::_('COM_NEWSWORLD_LEGEND_PERMISSIONS') ?></legend>
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $this->form->renderFieldset('accesscontrol');  ?>
                </div>
            </div>
        </fieldset>
    <?php echo HtmlHelper::_('uitab.endTab'); ?>
    
    <?php $this->ignore_fieldsets = array('details', 'params', 'item_associations', 'accesscontrol'); ?>
    <?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
    
    <?php echo HtmlHelper::_('uitab.endTabSet'); ?>

    </div>
    <input type="hidden" name="task" value="newsworld.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>