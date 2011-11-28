<?php
/*
	* @package Facebook plugin for TechjoomlaAPI
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
// include the Facebook class
if(JVERSION >='1.6.0')
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook'.DS.'plug_techjoomlaAPI_facebook'.DS.'lib'.DS.'facebook.php');
else
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook'.DS.'lib'.DS.'facebook.php');

$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_facebook', JPATH_ADMINISTRATOR);
	
class plgTechjoomlaAPIplug_techjoomlaAPI_facebook extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_facebook(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$this->appKey	=& $this->params->get('appKey');
		$this->appSecret	=& $this->params->get('appSecret');
		$this->callbackUrl='';
		$this->errorlogfile='facebook_error_log.php';
		$this->user = JFactory::getUser();
		$this->db=JFactory::getDBO();
		$this->facebook = new Facebook(array(
 	 'appId'  => $this->appKey,
   'secret' => $this->appSecret,
   'callbackUrl'=> $this->callbackUrl,
   'cookie' => true, // enable optional cookie support
		));
		
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
   	$plug['name']="Facebook";
   	
  	//check if keys are set
		if($this->appKey=='' || $this->appSecret=='' || !in_array($this->_name,$config))
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="facebook.png"; 
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
		if($result)
		{	
		$uaccess=json_decode($result);		
		if ($uaccess->facebook_uid && $uaccess->facebook_secret)
			return 1;
		else
			return 0;
		}
		else
		return 0;
	}
	
	function get_request_token($callback) 
	{
		
		$this->callbackUrl=$callback;
		$params = array(
							'redirect_uri' => $callback,
							'scope' =>'email,read_stream,user_status,publish_stream,offline_access', 
							);
			
		try	{
			$loginUrl = $this->facebook->getLoginUrl($params);
			$user = $this->facebook->getUser();
		} 
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
		}	
			$response=header('Location:'.$loginUrl);
			$return=$this->raiseLog($user,JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0);
			
		
			return true; 
	
	}
	
	function get_access_token($get,$client,$callback) 
	{
		
		try{	
			$uid = $this->facebook->getUser();			
			$facebook_secret = $this->facebook->getAccessToken();
		}
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
    }	
    
		$data = array('facebook_uid'=>$uid,'facebook_secret'=>$facebook_secret);
		$return=$this->raiseLog($data,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0); 
		$this->store($client,$data);		
		return true;
		
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
	
	        
	function plug_techjoomlaAPI_facebookget_contacts() 
	{
		
		try{	
			$contacts=array();
			$friends= $this->facebook->api('/me/friends');
		}
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
    }	
		
		
		
		$connections =$friends;	
		
		$cnt=0;
		$emails=array(array());
		foreach ($connections['data'] as $contact)
			{
				
				$emails[$cnt]['id']= $contact['id'];	
				$emails[$cnt]['name']= $contact['name'];
				$emails[$cnt]['picture-url']= 'https://graph.facebook.com/'.$emails[$cnt]['id'].'/picture';																						
				$cnt++;
				
			}
			
			$contacts=$this->renderContacts($emails);
			if(count($contacts)==0)
			{
				$this->raiseException(JText::_('NO_CONTACTS'));
				$this->raiseLog(JText::_('NO_CONTACTS'),JText::_('LOG_GET_CONTACTS'),$this->user->id,0);
			}
			else
			
			$this->raiseLog(JText::_('CONTACTS_FOUND'),JText::_('LOG_GET_CONTACTS'),$this->user->id,0);
		
		return $contacts;
		
	}
	
	function renderContacts($emails)
	{
			
			$count=0;
			foreach($emails as $connection)
			{
				if($connection['id'])	
				{	
					$r_connections[$count]->id  =$connection['id'];
					$first_name ='';
					$last_name ='';
					if(array_key_exists('first-name',$connection))
						$first_name =$connection['first-name'];
					if(array_key_exists('last-name',$connection))
						$last_name  =$connection['last-name'];
					if(array_key_exists('first-name',$connection) or array_key_exists('last-name',$connection))											
					$r_connections[$count]->name=$first_name.' '.$last_name;
					else if(array_key_exists('name',$connection))
					$r_connections[$count]->name=$connection['name'];
					if($connection['picture-url']	)
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
		return $r_connections;
	}
	
	function plug_techjoomlaAPI_facebooksend_message($raw_mail,$invitee_data)
	{	
		foreach($invitee_data as $id=>$invitee_name)
		 {
			$inviteid[]=$id;	
			}
	
		$inviteeidstr=implode(',',$inviteid);
		$userid=md5($this->user->id);
		$regurl= cominvitexHelper::getinviteURL();
		
		$parameters = array(
		'app_id' => $this->facebook->getAppId(),
		'to' => $inviteeidstr,
		'link' => $regurl,
		'redirect_uri' => JURI::base(),
		'name'=>'This is the Subject',
		'description'=>'This is the Message from Config'
 		);
 		
		$url = 'http://www.facebook.com/dialog/send?'.http_build_query($parameters);
		header('Location:'.$url);	
		die;
	
  }//end send message
  
  function plug_techjoomlaAPI_facebookgetstatus()
	{ 
		$oauth_keys =array();
	 	$oauth_keys = $this->getToken();
	 	$returndata=array(array());
	 	$i = 0;
	 	if($this->params->get('broadcast_limit'))
	 	$facebook_profile_limit=$this->params->get('broadcast_limit');
	 	else
	 	$facebook_profile_limit=2;
		$returndata = array();
		if(!$oauth_keys)
		return false;
	 	foreach($oauth_keys as $oauth_key){	
	 		
			$token =json_decode($oauth_key->token);	
			try{		
				$json_facebook = $this->facebook->api($token->facebook_uid.'/statuses',array('access_token'=>$token->facebook_secret,'limit'=>$facebook_profile_limit));
			}
			catch (FacebookApiException $e) 
			{
				$this->raiseException($e->getMessage());
				return false;
		  }
		  $status='';
		  $status=$this->renderstatus($json_facebook['data'])	;
		  if($status)
			{
				$returndata[$i]['user_id'] 	= $oauth_key->user_id;
				$returndata[$i]['status'] 	= $status;	
				$response=$this->raiseLog(JText::_('LOG_GET_STATUS_SUCCESS'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,1);
			}
			else
			{
				
				$response=$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,0);
			}
			
			$i++;
		}
		
		if(!empty($returndata['0']))
		return $returndata;
		else
		return;
		
	}
			

	function renderstatus($totalresponse)
	{	
		$status = array();
	 	$j=0;
		for($i=0; $i <= count($totalresponse); $i++ )
		{			
				if(isset($totalresponse[$i]['message']))
				{
					$status[$j]['comment'] =  $totalresponse[$i]['message'];
					$status[$j]['timestamp'] = strtotime($totalresponse[$i]['updated_time']);
					$j++;
				}
		  }
		return $status;
	}

	function plug_techjoomlaAPI_facebooksetstatus($userid='',$content='')
	{
	
		$oauth_key = $this->getToken($userid);
		
		if(!$oauth_key)
		return false;
		else
		$token =json_decode($oauth_key[0]->token);	
		
		$post=array();
		if(!$content)
		return array();
		
		try{
		if(isset($token))
		$post = $this->facebook->api($token->facebook_uid.'/feed', 'POST', array('access_token'=>$token->facebook_secret,'message' => $content));
		
		} 
		catch (FacebookApiException $e) 
		{
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_FAIL').JText::_('LOG_SET_STATUS'),$e->getMessage(),$userid,1);
		  return false;
    }
		if($post)
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_SUCCESS').JText::_('LOG_SET_STATUS'),$content,$userid,1);
		else
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_FAIL').JText::_('LOG_SET_STATUS'),$e->getMessage(),$userid,1);
			
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
		
		
		
		if(is_array($status_log))
		{
			$status=$status_log;
			if(isset($status['info']['http_code']))
			{
				$params['http_code']		=	$status['info']['http_code'];
				if(!$status['success'])
				{
						if(isset($status['facebook']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['facebook']);
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
		$this->raiseException($status_log,$userid,$display,$params);	
		return true;	
	}
	
	function plug_techjoomlaAPI_facebookget_profile($integr_with,$client,$callback)
	{
			
			$mapData[0]		=& $this->params->get('mapping_field_0');	//joomla		
			$mapData[1]		=& $this->params->get('mapping_field_1'); //jomsocial
			$mapData[2]		=& $this->params->get('mapping_field_2'); //cb
			
		try{			
			$profileData= $this->facebook->api('/me');
			$profileData['picture-url']='https://graph.facebook.com/'.$profileData['id'].'/picture';
		} 
		catch (FacebookApiException $e) 
		{
			$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
			return false;
		}

		if($profileData)
		{
			$profileDetails['profileData']=$profileData;	
			$profileDetails['mapData']=$mapData;
			return $profileDetails;
		}
			
  }

}//end class
