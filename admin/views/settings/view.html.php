<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class profileimportViewsettings extends JView
{
	function display($tpl = null)
	{
	 	JHTML::_('behavior.mootools');
		$this->_setToolBar();
		$apiplugin = $this->get('APIpluginData');
		$this->assignRef('apiplugin', $apiplugin);
		$this->setLayout('settings');
		parent::display($tpl);
	}
	function _setToolBar()
	{	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_profileimport/css/profileimport.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'PI_SOCIAL' ), 'icon-48-profileimport.png' );
		JToolBarHelper::save('save',JText::_('PI_SAVE') );
		JToolBarHelper::cancel( 'cancel', JText::_('PI_CLOSE') );
	}
}
?>
