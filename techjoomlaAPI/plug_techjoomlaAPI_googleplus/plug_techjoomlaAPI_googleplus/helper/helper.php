<?php


class plug_techjoomlaAPI_googleplusHelper
{ 	
	
	function plug_techjoomlaAPI_googleplusRender_profile($profileData)
	{
		$data = $profileData;  				
		$r_profileData=array();
		if(isset($data['displayName']))
		$r_profileData['name'] 	=	$data['displayName'];
	
		if(isset($data['gender']))
		$r_profileData['gender'] 			=	ucwords($data['gender']);
				
	
		if(isset($data['image']))
			$r_profileData['image']			=	$data['image']['url'];
	
		if(isset($data['aboutMe']))
		{
	
			$r_profileData['summary']		=	$data['aboutMe'];
		}
	
		if(isset($data['organizations']['0']['type']) and $data['organizations']['0']['type']=='school')
			$r_profileData['education']=	$data['organizations']['0']['name'];
	
		if(isset($data['organizations']['type']) and $data['organizations']['type']=='school')
			$r_profileData['education']=	$data['organizations']['name'];
	
		return $r_profileData;

	}

}





