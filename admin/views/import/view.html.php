<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class profileimportViewimport extends JView
{
	function display($tpl = null)
	{
		$user=JFactory::getUser();
		if($user->id){
			$model		= $this->getModel( 'import');
			$apidata 	=	$model->getRenderAPIicons();
			$this->assignRef('apidata',$apidata);
		}
		else
		echo JText::_('PI_LOGIN');
		parent::display($tpl);
	}
}

