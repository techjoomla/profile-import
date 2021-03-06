<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');


class profileimportControllerimport extends JController
{
	var $bconfig = '';	
	function display()
	{
		parent::display();
	}
		//START apis
	/*call model for request token*/
	function get_request_token()
	{
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$model=&$this->getModel('import');
		$api_used =JRequest::getVar('api_used'); 
		$session->set('api_used',$api_used);
		$grt_response=$model->getRequestToken($api_used);
	}
	
	/*call model for access token*/
	function get_access_token()
	{
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$msg = '';
		$get=JRequest::get('get'); 
		$model=&$this->getModel('import');
		$response=$model->getAccessToken($get);
		
		if($response){
			$menu = &JSite::getMenu();
			$items= $menu->getItems('link', 'index.php?option=com_profileimport&view=import');//pass the link for which you want the ItemId.
			if(isset($items[0])){
				$itemid = $items[0]->id;
			}
			$mainframe->redirect('index.php?option=com_profileimport&controller=import&task=importProfile');
			
		}
	 	
	 	
	}
	/*call to destroy the Import Profile of a user*/
	function importProfile()
	{ 
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$api_used =JRequest::getVar('api');
		$model = $this->getModel('import');
		$return=$model->importProfile($api_used);
		
		$itemid=$session->get("PFI_itemid",'');
		if($return['return']==1)
		{
			/*$integr_with=$return['integr_with'];
			if($integr_with==0)
				$integr_nm=JText::_("PF_INT_JOOMLA");
			else if($integr_with==1)
				$integr_nm=JText::_("PF_INT_JS");
			else if($integr_with==2)
				$integr_nm=JText::_("PF_INT_CB");*/
				$integr_nm='';
				
				$updated_data='';
				if($return['profilefields'])
				{
					$updated_data=sprintf(JText::_("PF_UPDATED_INFO"),implode(',',$return['profilefields']),$integr_nm);
					$msg=JText::_("PF_IMPORT_SUCCESS").$updated_data;
				}
				else
				{
					 $msg=JText::_("PF_IMPORT_SUCCESS");

				}
					
		}
		
		else
			$msg=JText::_("PF_IMPORT_SUCCESS");
		$mainframe->redirect( JRoute::_(JURI::base().'index.php?option=com_profileimport&view=import&itemid='.$itemid), $msg);
	}
} //class


