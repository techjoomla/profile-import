<?php


class plug_techjoomlaAPI_facebookHelper
{ 	
	
	function plug_techjoomlaAPI_facebookRender_profile($profileData)
  {
  	
  	$data = $profileData;
 		$r_profileData=array();
		if(isset($data['first-name']))
		$r_profileData['first-name'] 	=	$data['first-name'];
		
		if(isset($data['last-name']))
		$r_profileData['last-name'] 	=	$data['last-name'];
		
		if(isset($data['name']))
		$r_profileData['name'] 				=	$data['name'];
		
		if(isset($data['gender']))
		$r_profileData['gender'] 				=	ucwords($data['gender']);
		
		if(isset($data['email']))
		$r_profileData['email'] 				=	$data['email'];
		
		if(count($data['work'])>1)
		$r_profileData['designation']=	$data['work']['0']['position']['name'];
		
		if(count($data['work'])==1)
		$r_profileData['designation']=	$data['work']['position']['name'];
		
		if(isset($data['location']['name']))
		$r_profileData['location']=	$data['location']['name'];
		
		if(isset($data['education']) and (count($data['education'])>1))
		$r_profileData['education']		=$data['education']['0']['degree']['name'];
		
		if(isset($data['education']) and (count($data['education'])==1))
			$r_profileData['education']		=$data['educations']['education']['degree'];
		
		
		if(count($data['phone-numbers']['phone-number']['phone-number'])>1)
		$r_profileData['phone-number']		=$data['phone-numbers']['phone-number']['0']['phone-number'];
		
		if(count($data['phone-numbers']['phone-number'])==1)
		$r_profileData['phone-number']		=$data['phone-numbers']['phone-number']['phone-number'];
		
		
		if(isset($data['main-address']))
		$r_profileData['address']=$data['main-address'];
		
		if(isset($data['date-of-birth']))
		{
			if($data['date-of-birth']['month']<10)
			$data['date-of-birth']['month']='0'.$data['date-of-birth']['month'];
		
			if($data['date-of-birth']['day']<10)
			$data['date-of-birth']['day']='0'.$data['date-of-birth']['day'];
			
			$r_profileData['birthdate']=	$data['date-of-birth']['year'].'-'.$data['date-of-birth']['month'].'-'.$data['date-of-birth']['day'];
		}
		
		if(isset($data['current-status']))
		$r_profileData['current-status']=	$data['current-status'];
		
		if(isset($data['picture-url']))
		$r_profileData['image']=$data['picture-url'];
		return $r_profileData;
  	
  	
  }

	

}





