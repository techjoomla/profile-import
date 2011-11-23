<?php


class plug_techjoomlaAPI_googleplusHelper
{ 	
	
	function plug_techjoomlaAPI_googleplusRender_profile($profileData)
	{
		$data = $profileData['profiledata'];  	
		
		$r_profileData=array();		
		$excludeFields=array('status','profile_image_url','url');
		$gpfields=$profileData['mapping_field'];
	 	$r_profileData=array();
		//$gpfields=array('displayName','aboutMe','birthday','gender','email','work','currentLocation','relationshipStatus',);
	
		foreach($gpfields as $key=>$arrkey)
		{

			if(!is_array($data[$arrkey]))
			{
				
				if($arrkey=="gender" )
				$r_profileData[$arrkey]=ucwords($data[$arrkey]);
				else								
				$r_profileData[$arrkey]=$data[$arrkey];
			}
			
		
		}
		
		if(isset($data['organizations']))
		$r_profileData['organizations']=$this->renderorganizations($data['organizations']);

		if(isset($data['placesLived']))
		$r_profileData['placesLived']=$this->renderlocation($data['placesLived']);

		if(isset($data['urls']))
		$r_profileData['urls']=$this->renderurlsemails($data['urls']);

		if(isset($data['emails']))
		$r_profileData['emails']=$this->renderurlsemails($data['emails']);

		if(isset($data['image']))
		$r_profileData['image']=$data['image']['url'];
		return $r_profileData;

	}
	
	function renderorganizations($organizations)
	{
	
				foreach($organizations as $edukey=>$eduvalue)
				{
					
					
						if($eduvalue['type']=='work')
						{
							
								$orgs[]=$eduvalue['name'];
						
						}
			
				}
				$orgstr=implode(',',$orgs);
				return $orgstr;
		

	}
	
	function rendereducations($organizations)
	{
				foreach($organizations as $edukey=>$eduvalue)
				{
					if($eduvalue['type']=='school')
						{
							if(isset($r_profileData))					
								$r_profileData=$r_profileData.", ".$eduvalue['name'];
							else
								$r_profileData=$eduvalue['name'];
						
						}
				}
				return $r_profileData;
	}
	function renderlocation($location)
	{
		
		foreach($location as $key=>$value)
		{
			$address=$value;
		}
		
		$addresstr=implode(',',$address);
		return $addresstr;
	}
	
	function renderurlsemails($urlemail)
	{
		
		foreach($urlemail as $key=>$value)
		{
			$address[]=$value['value'];
		}
		
		$addresstr=implode(',',$address);
		return $addresstr;
	}
	
	

}






