<?php


class plug_techjoomlaAPI_twitterHelper
{ 	
	
	function plug_techjoomlaAPI_twitterRender_profile($profileData)
  {
  	$data = $profileData['profiledata'];  	
		$r_profileData=array();		
		$excludeFields=array('status','profile_image_url','url');
		$twitterfields=$profileData['mapping_field'];
	 	$r_profileData=array();
		
		foreach($twitterfields as $key=>$arrkey)
		{
			if(!in_array($arrkey,$excludeFields))
			 {
				if(!is_array($data[$arrkey]))
					$r_profileData[$arrkey]=$data[$arrkey];
				}
		}
		
		if(isset($data['status']['text']))
		$r_profileData['current-status']=	$data['status']['text'];
		
		if(isset($data['profile_image_url']))
		$r_profileData['profile_image_url']=$data['profile_image_url'];
		
		if(isset($data['url']))
		$r_profileData['url']=$data['url'];
		
		if(isset($data['status']['text']))
		$r_profileData['status']=$data['status']['text'];
		
		return $r_profileData;
  	
  }
	


}





