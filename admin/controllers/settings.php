<?php
defined('_JEXEC') or die();

class profileimportControllerSettings extends profileimportController
{
	function __construct()
	  {	
		parent::__construct();
	  }
	
	function save()
	 {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model	=& $this->getModel( 'settings' );
		$post	= JRequest::get('post');
		$model->setState( 'request', $post );	
	    switch (JRequest::getCmd('task') ) 
		{
			case 'cancel':
				$this->setRedirect( 'index.php?option=com_profileimport');
			break;
			case 'save':
			
				if ($model->store()) 
					$msg = JText::_('CONFIG_SAVED');
				else 
					$msg = JText::_('CONFIG_SAVE_PROBLEM');
				$this->setRedirect( "index.php?option=com_profileimport&view=settings", $msg );
			break;
		}
	 }//function save ends
}
?>
