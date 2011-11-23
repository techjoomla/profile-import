<?php


class plug_techjoomlaAPI_twitterHelper
{ 	
	
	function plug_techjoomlaAPI_twitterRender_profile($profileData)
  {
  	$data = $profileData;
 	
  	$r_profileData=array();
		if(isset($data['name']))
		$r_profileData['name'] 	=	$data['name'];
		if(isset($data['screen_name']))
		$r_profileData['screen_name'] 				=	$data['screen_name'];
	
		if(isset($data['description']))
		$r_profileData['description'] 				=	$data['description'];
					
		
		if(isset($data['location']))
		$r_profileData['address']=$data['location'];
		
		if(isset($data['profile_image_url']))
		{
		
			$r_profileData['image']=	$data['profile_image_url'];
		}
		
		if(isset($data['status']['text']))
		$r_profileData['current-status']=	$data['status']['text'];
		
		return $r_profileData;
  	
  }
	


}





