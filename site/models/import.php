<?php
/**
 * @package InviteX
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.database.database.mysql' );

/*included to get jomsocial avatar*/
$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
if(JFolder::exists($jspath))
include_once($jspath.DS.'libraries'.DS.'core.php');


class profileimportModelimport extends JModel
{

	function getRenderAPIicons()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components/com_profileimport/config/config.php');
		$api_config=array();
		if(isset($profileimport_config['api']) && !empty($profileimport_config['api']))
		{
			$api_config=$profileimport_config['api'];
			$dispatcher = &JDispatcher::getInstance();
			JPluginHelper::importPlugin('techjoomlaAPI');
			$result =$dispatcher->trigger('renderPluginHTML',array($api_config));//trigger all "profileimport" plugins method that renders the button/image
			return $result;
		}
	
	}


	function getRequestToken($api_used)
	{
		$callback=JURI::base().'index.php?option=com_profileimport&controller=import&task=get_access_token';	
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		$grt_response=$dispatcher->trigger('get_request_token',array($callback));
		if(!$grt_response[0])	{
			return FALSE;
		}
	}

	function getAccessToken($get)
	{
		$client="profileimport";
		$callback = JURI::base()."index.php?option=com_profileimport&controller=import&task=get_access_token";
		$session = JFactory::getSession();
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$session->get('api_used'));
		$grt_response =	$dispatcher->trigger('get_access_token',array($get,$client,$callback));
		if(!$grt_response[0])	{
				return FALSE;
			}
			else{
				return TRUE;
			}
	}

	function importProfile()
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components/com_profileimport/config/config.php');
		
		$integr_with	=	$profileimport_config['reg_direct'];		
		$session		 	= JFactory::getSession();
		$client				=	"profileimport";
		$callback			=	'';
		$pluginName		=	$session->get('api_used');
		$dispatcher 	=	&JDispatcher::getInstance();
		
		
		// include the Helper class of plugin
		
		if(JVERSION >='1.6.0')
			require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.$pluginName.DS.$pluginName.DS.'helper'.DS.'helper.php');
		else
			require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.$pluginName.DS.'helper'.DS.'helper.php');
		
		//call to function from plugin helper file and get Raw data	
		
		JPluginHelper::importPlugin('techjoomlaAPI',$pluginName);
		$profileDetails 		=	$dispatcher->trigger($session->get('api_used').'get_profile',array($integr_with,$client,$callback));
		
		//call to function from plugin helper file and get Rendered Array	
		
		$pluginHelperClassName		=	$pluginName.'Helper';
		$pluginHelper 						= new $pluginHelperClassName;
		$profilefunction					=	$pluginName.'Render_profile';		
		$profileData							=	call_user_func(array($pluginHelper, $profilefunction ),$profileDetails[0]['profileData']);
		$mapData									=	$profileDetails[0]['mapData'];
		if(!empty($profileDetails[0]['profileData']))
		{
			$return=$this->importProfileRenderData($profileData,$mapData,$integr_with);
		}
		return $return;
	}
	
	function importProfileRenderData($profileData,$mapData,$integr_with)
	{
			$db 			= JFactory::getDBO();
			$user			= JFactory::getUser();
			
			$userid=$user->id;
			if($integr_with=='0')
				$lines		= $mapData['0']; 
			if($integr_with=='1')
				$lines		= $mapData['1']; 		
			if($integr_with=='2')
				$lines		= $mapData['2']; 				
			$userid 	= $user->id;
			$maptext 	= explode("\n",trim($lines));
			
			$js_img_upload=0;
			foreach ($profileData as $key=>$pfData) 
			{
				foreach ($maptext as $mapkey=>$fieldmap) 
				{
					
					$maparr		=array('0'=>0,'1'=>0,'2'=>0);					
					$maparr 	= explode("=",trim($fieldmap));
										
					if(isset($pfData) and isset($maparr['1']))
					{
							if(trim($maparr['1'])==trim($key))
							{
								if($integr_with=='0')
									$return=$this->importProfile_Joomla($profileData,$mapData['0'],$db,$userid,$maparr,$pfData);
								if($integr_with=='1')	
									$return=$this->importProfile_JS($profileData,$mapData['1'],$db,$userid,$maparr,$pfData);										
								if($integr_with=='2')
									$return=$this->importProfile_CB($profileData,$mapData['2'],$db,$userid,$maparr,$pfData);
							}
							else
							{
						
								if($key=="image" and $integr_with=='1' and $js_img_upload==0)//Jomsocial Avatar Upload
								{									
									$js_img_upload=1;	
									$this->imageUploadJS($pfData,$userid,$db);
								}
							
							
							}//else
						
						}//if
						
					}//foreach

			} //foreach
			
		return $return;
			
			
	}

	function importProfile_Joomla($profileData,$mapData,$db,$userid,$maparr,$pfData)
	{

	}
		
	function importProfile_JS($profileData,$mapData,$db,$userid,$maparr,$pfData)
	{
		$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
		if(JFolder::exists($jspath))
		include_once($jspath.DS.'libraries'.DS.'core.php');
		include_once($jspath.DS.'models'.DS.'profile.php');
		
		$jomsocial=new CommunityModelProfile();
		$query = "SELECT id FROM #__community_fields WHERE fieldcode =".$db->Quote(trim($maparr[0]));
		$db->setQuery($query);
		$fieldid = $db->loadResult();
		if($fieldid) {
			$return=$jomsocial->updateUserData(trim($maparr[0]),$userid,$pfData);	
			return 1;			
		}	
		
	}
	
	function importProfile_CB($profileData,$mapData,$db,$userid,$maparr,$pfData)
	{
			$query = "SELECT name FROM #__comprofiler_fields WHERE name=".$db->Quote($maparr[0]);	
			$db->setQuery($query);
			$fieldname = $db->loadResult();
			
			if($fieldname) {	
				if($fieldname=='avatar')
				{
				$pfData=$this->imageUploadCB($pfData,$userid);			
				}

				$query = "UPDATE #__comprofiler SET `".$fieldname."`= '$pfData' WHERE  user_id = $userid ";
				$db->setQuery($query);
				$db->query();				
				return 1;
			}	
	}

	function imageUploadJS($pfData,$userid,$db)
	{
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');			
			$handle 			= fopen($pfData, "r");
			$contents 		= stream_get_contents($handle);
			$query = "SELECT md5($userid)";
			$db->setQuery($query);
			$imgnm = $db->loadResult();
			$destination	= JPATH_SITE .DS.'images'.DS.'avatar'.DS;
			JFile::write($destination.$imgnm.'.png',$contents);		
			JFile::write($destination.'thumb_'.$imgnm.'.png',$contents);	

			$jsAvatarfile='images/avatar/'.$imgnm.'.png';
			$jsThumbnail='images/avatar/thumb_'.$imgnm.'.png';									
			$query = "UPDATE #__community_users SET `avatar`= '$jsAvatarfile' , `thumb`='$jsThumbnail' WHERE  userid = $userid ";
			$db->setQuery($query);
			$db->query();
	
	}
	
	function imageUploadCB($pfData,$userid)
	{
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');			
			
			$handle = fopen($pfData, "r");
			$contents = stream_get_contents($handle);
			$destination = JPATH_SITE .DS.'images'.DS.'comprofiler'.DS.'gallery'.DS ;
			JFile::write($destination.$userid.'.png',$contents);				
			$pfData='gallery/'.$userid.'.png';
			return $pfData;
	}

}//end class
