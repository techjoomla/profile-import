<?php
/*
	* @package LinkedIn plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

// include the LinkedIn class
if(JVERSION >='1.6.0')
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_googleplus'.DS.'plug_techjoomlaAPI_googleplus'.DS.'lib'.DS."apiClient.php");
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_googleplus'.DS.'plug_techjoomlaAPI_googleplus'.DS.'lib'.DS."contrib".DS."apiPlusService.php");
}
else
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_googleplus'.DS.'lib'.DS."apiClient.php");
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_googleplus'.DS.'lib'.DS."contrib".DS."apiPlusService.php");
}

$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_googleplus', JPATH_ADMINISTRATOR);	
class plgTechjoomlaAPIplug_techjoomlaAPI_googleplus extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_googleplus(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$this->appKey	=& $this->params->get('appKey');
		$this->appSecret	=& $this->params->get('appSecret');
		$this->developerKey=& $this->params->get('developerKey');
		
		$this->callbackUrl='';
		$this->errorlogfile='googleplus_error_log.php';
		$this->user =& JFactory::getUser();
		$this->db=JFactory::getDBO();
		$this->client = new apiClient();
		$this->client->setApplicationName("Google+ PHP Starter Application");
		
		 $this->client->setClientId($this->appKey);
 		$this->client->setClientSecret($this->appSecret);
 		$this->client->setDeveloperKey($this->developerKey);
 
 		
		$this->client->setScopes(array('https://www.googleapis.com/auth/plus.me'));
		$this->plus = new apiPlusService($this->client);

		
		}
	
	/*
		 * Get the plugin output as a separate html form 
     *
     * @return  string  The html form for this plugin
     * NOTE: all hidden inputs returned are very important
	*/
  function renderPluginHTML($config) 
	{	
		$plug=array(); 
   	$plug['name']="Googleplus";
  	//check if keys are set
		if($this->appKey=='' || $this->appSecret=='' || $this->developerKey==''  )// || !in_array($this->_name,$config)) #TODO add condition to check config
		{	
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="googleplus.png";   
		if(isset($config['client']))
		$client=$config['client'];
		else
		$client='';
		$plug['apistatus'] = $this->connectionstatus($client);
		return $plug;   
	}
	
	function connectionstatus($client=''){
		$where='';
		if($client)
		$where=" AND client='".$client."'";		
	
	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE user_id = {$this->user->id}  AND api='{$this->_name}'".$where;
		$this->db->setQuery($query);
		$result	= $this->db->loadResult();	
		//print_r();die;
		if ($result)
			return 1;
		else
			return 0;
	}
	
	
	function get_request_token($callback) 
	{
		
		$session = JFactory::getSession();
		$this->client->setRedirectUri($callback);
		$authUrl =  $this->client->createAuthUrl();
		header('Location:'.$authUrl);
	}
	
	function get_access_token($get,$client,$callback) 
	{
		$_SESSION['access_token']='';
		$this->client->setRedirectUri($callback);
		$session = JFactory::getSession();
		if (isset($get['code'])) {
			$response=$this->client->authenticate();
			$_SESSION['access_token'] = $this->client->getAccessToken();
			if($_SESSION['access_token'])		
			{
			$this->store($client,$_SESSION['access_token']);

			return true;
			}
			else
			return false;
  	}
		
		
	}

	function store($client,$data) #TODO insert client also in db 
	{
		
	 	$qry = "SELECT id FROM #__techjoomlaAPI_users WHERE user_id ={$this->user->id} AND client='{$client}' AND api='{$this->_name}' ";
		$this->db->setQuery($qry);
		$id	=$exists = $this->db->loadResult();
		$row = new stdClass;
		$row->id=NULL;
		$row->user_id = $this->user->id;
		$row->api 		= $this->_name;
		$row->client=$client;
		$row->token=json_encode($data);
		
		if($exists)
		 {
		 		
		 		$row->id=$id;
	 			$this->db->updateObject('#__techjoomlaAPI_users', $row, 'id');
		 }
		 else
		 {
		 			
				$status=$this->db->insertObject('#__techjoomlaAPI_users', $row);
		 }
		
	}
	
	function getToken($user=''){
		$user=$this->user->id;
		$where = '';
		if($user)
			$where = ' AND user_id='.$user;
			
		$query = "SELECT user_id,token
		FROM #__techjoomlaAPI_users 
		WHERE token<>'' AND api='{$this->_name}' ".$where ;
		$this->db->setQuery($query);
		return $this->db->loadObjectlist();
	}
	function remove_token($client)
	{ 
		if($client!='')
		$where="AND client='{$client}' AND api='{$this->_name}'";
		
		#TODO add condition for client also
		$qry 	= "UPDATE #__techjoomlaAPI_users SET token='' WHERE user_id = {$this->user->id} ".$where;
		$this->db->setQuery($qry);	
		$this->db->query();
	}
	
	function plug_techjoomlaAPI_googleplusget_contacts() 
	{
		$session = JFactory::getSession();
		$this->API_CONFIG['callbackUrl']= JRoute::_(JURI::base().'index.php?option=com_invitex&view=invites&layout=apis');
		
		if($session->get("['oauth']['googleplus']['authorized']",'') === TRUE)
    {
			// user is already connected
			try{
				$this->googleplus = new LinkedInAPI($this->API_CONFIG);
				$this->googleplus->setTokenAccess($session->get("['oauth']['googleplus']['access']",''));			
				$response = $this->googleplus->connections('~/connections:(id,first-name,last-name,picture-url)');			
			}
			catch(LinkedInException $e)
			{ 
				$this->raiseException($e->getMessage());
				return false;
			}
			
			$return=$this->raiseLog($response,JText::_('LOG_GET_CONTACTS'),$this->user->id,0);
			if($response['success'] === TRUE)
			{
				$connections = simplexml_load_string($response['googleplus']);
				$contacts=array();
				$contacts=$this->renderContacts($connections);
				if(count($contacts)==0)
				$this->raiseException(JText::_('NO_CONTACTS'));
				
				
			} 
			
    }
  
		return $contacts;
	}
	
	function renderContacts($connections)
	{
		$mainframe=JFactory::getApplication();		
		$conns = (array) $connections;
		if(isset($conns['person']))
		{
				$conns = $conns['person'];
		
				$count=0;
				$r_connections=array();
				if (array_key_exists("0",$conns))
				{
					foreach($conns as $connection)
					{
						$connection  = (array) $connection;
						if($connection['id'])
						{
							$r_connections[$count]->id  =$connection['id'];
							$r_connections[$count]->name =$connection['first-name'].' '.$connection['last-name'];
							if(array_key_exists('picture-url',$connection))
							{
								$r_connections[$count]->picture_url=$connection['picture-url'];
							}
							else
							{
								$r_connections[$count]->picture_url='';
							}
							$count++;
						}
					}
				}
				else//only 1 connection
				{	
					$connection  = (array) $conns;
					if($connection['id'])
					{
						$r_connections[0]->id  =$connection['id'];
						$r_connections[0]->first_name =$connection['first-name'].' '.$connection['last-name'];
						if($connection['picture-url']	)
						{
							$r_connections[0]->picture_url=$connection['picture-url'];
						}
						else
						{
							$r_connections[0]->picture_url='';
						}
					}
				}
				return $r_connections;
		}
		
	}
	
	function plug_techjoomlaAPI_googleplussend_message($post)
	{
		$session = JFactory::getSession();	
		if($session->get("['oauth']['googleplus']['authorized']",'') === TRUE)
    {
			if(!empty($post['contacts']))
			{
				$this->API_CONFIG['callbackUrl']=NULL;
				$this->googleplus = new LinkedInAPI($this->API_CONFIG);
				$this->googleplus->setTokenAccess($session->get("['oauth']['googleplus']['access']",''));
				
				if(!empty($post['message_copy']))
				{
					$copy = TRUE;
				}
				else
				{
					$copy = FALSE;
				}
				
				try{
				$response = $this->googleplus->message($post['contacts'], $post['message_subject'], $post['message_body'],$copy);
				}
				catch(LinkedInException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				
				$return=$this->raiseLog($response,JText::_('LOG_SEND_MESSAGE'),$this->user->id,0);
				return $return;
			
			} 
			
            
    }
  }//end send message
 
 
	function plug_techjoomlaAPI_googleplusgetstatus()
	{  	
		$i = 0;
		$returndata = array();
		$oauth_keys = $this->getToken(); 
		if($this->params->get('broadcast_limit'))
	 	$googleplus_profile_limit=$this->params->get('broadcast_limit');
	 	else
	 	$googleplus_profile_limit=2;		
		foreach($oauth_keys as $oauth_key){
			$this->client->setAccessToken(json_decode($oauth_key->token,true));
			$this->plus = new apiPlusService($this->client);
			$me = $this->plus->people->get('me');
			$optParams = array('maxResults' => 100);
			$activities = $this->plus->activities->listActivities('me', 'public', $optParams);
  		print_r($activities);die;
			
			
		}
		return $returndata;
	}
  	function renderstatus($totalresponse)
  	{
			$status = array();
			$j=0;
			for($i=0; $i <= count($totalresponse->values); $i++ )
			{
				if(isset($totalresponse->values[$i]->updateContent->person->currentShare->comment)){
					$status[$j]['comment'] =  $totalresponse->values[$i]->updateContent->person->currentShare->comment;
					$status[$j]['timestamp'] = $totalresponse->values[$i]->updateContent->person->currentShare->timestamp;
					$status[$j]['timestamp'] = number_format($status[$j]['timestamp'],0,'','');
          $status[$j]['timestamp'] = intval($status[$j]['timestamp'] /1000); 
          $config =& JFactory::getConfig();
					$offset = $config->getValue('config.offset'); 
					$get_date= & JFactory::getDate($status[$j]['timestamp'],$offset);				
					$status[$j]['timestamp'] = $get_date->toFormat();
					$j++;
				}
			} 
	  	return $status;
		}
	function plug_techjoomlaAPI_googleplussetstatus($userid,$comment='')
	{
	
		//To do use json encode decode for this	
		$oauth_key = $this->getToken($userid); 
		if(!isset($oauth_key))
		return false;
		$oauth_token		 	= json_decode($oauth_key[0]->token);
		$oauth	=	json_decode($oauth_token->googleplus_oauth, true);
		
		try{
			$this->googleplus->setTokenAccess($oauth);			

			$content = array ('comment' => $comment);
			//$content = array ('comment' => $comment, 'title' => '', 'submitted-url' => '', 'submitted-image-url' => '', 'description' => '');
			$status= $this->googleplus->share('new',$content); 
		
		}
		catch(LinkedInException $e)
		{
			
			$this->raiseException($e->getMessage(),$userid,1);
			return false;
		} 
		
		$response=$this->raiseLog($status,JText::_('LOG_SET_STATUS'),$userid,1);
		return $response;
	}
	
	function raiseException($exception,$userid='',$display=1,$params=array())
	{
		$path="";
		$params['name']=$this->_name;
		$params['group']=$this->_type;	
		if($this->params->get('log_file_path'))
		$path=& $this->params->get('log_file_path');
		techjoomlaHelperLogs::simpleLog($exception,$userid,'plugin',$this->errorlogfile,$path,$display,$params);
		return;
	}
	
	function raiseLog($status_log,$desc="",$userid="",$display="")
	{
		
		$params=array();		
		$params['desc']	=	$desc;
		if(is_object($status_log))
		$status=JArrayHelper::fromObject($status_log,true);
		
		
		
		if(is_array($status_log) or is_array($status))
		{
			$status=$status_log;
			if(isset($status['info']['http_code']))
			{
				$params['http_code']		=	$status['info']['http_code'];
				if(!$status['success'])
				{
						if(isset($status['googleplus']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['googleplus']);
				
			
					$params['success']			=	false;
					$this->raiseException($response_error['error']['message'],$userid,$display,$params);
					return false;
		
				}
				else
				{
					$params['success']	=	true;
					$this->raiseException(JText::_('LOG_SUCCESS'),$userid,$display,$params);		
					return true;
		
				}
			
			}
		}
		$this->raiseException(JText::_('LOG_SUCCESS'),$userid,$display,$params);	
		return true;	
	}
	
	
	function plug_techjoomlaAPI_googleplusget_profile($integr_with,$client,$callback)
	{
			$mapData[0]		=& $this->params->get('mapping_field_0');	//joomla		
			$mapData[1]		=& $this->params->get('mapping_field_1'); //jomsocial
			$mapData[2]		=& $this->params->get('mapping_field_2'); //cb
				
			$oauth_keys = $this->getToken(); 
		
			if($this->params->get('broadcast_limit'))
		 	$googleplus_profile_limit=$this->params->get('broadcast_limit');
		 	else
		 	$googleplus_profile_limit=2;	
		 		
			foreach($oauth_keys as $oauth_key){
				$this->client->setAccessToken(json_decode($oauth_key->token,true));
				$this->plus = new apiPlusService($this->client);
				$profileData = $this->plus->people->get('me');  	
	 					
				if($profileData){
						$profileDetails['profileData']=$profileData;	
						$profileDetails['mapData']=$mapData;
						return $profileDetails;
				}
			
			}	//for each
			
 	 }
  
  
}//end class
