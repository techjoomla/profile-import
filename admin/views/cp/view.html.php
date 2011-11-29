<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
class profileimportViewcp extends JView
{
	function display($tpl = null)
	{
		$this->_setToolBar();
		if(!JRequest::getVar('layout'))
			$this->setLayout('default');
		else{
			$queue = $this->get('queue');
			$this->assignRef('queues', $queue);
		}
		parent::display($tpl);
	}
	
	function _setToolBar()
	{	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_profileimport/css/profileimport.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'PI_SOCIAL' ), 'icon-48-profileimport.png' );
		
		if(JRequest::getVar('layout'))
		{
			JToolBarHelper::save('save',JText::_('PI_TOOL_QUEUE') );
			JToolBarHelper::cancel( 'cancel', JText::_('PI_CLOSE') );
		}	
	}
}
?>
