<?php


class plug_techjoomlaAPI_googleplusHelper
{ 	
	
	function plug_techjoomlaAPI_googleplusRender_profile($profileData)
	{
		$data = $profileData; 
		$r_profileData=array();
		$gpfields=array('displayName','aboutMe','birthday','gender','email','work','currentLocation','relationshipStatus',);
	
		$r_profileData['organizations']='';
		if(isset($data['organizations']))
		{
			foreach($data['organizations'] as $edukey=>$eduvalue)
			{
				if($eduvalue['type']=='school')
					{
						if(isset($r_profileData['organizations']))
					
							$r_profileData['organizations']=$r_profileData['organizations'].", ".$eduvalue['name'];
						else
							$r_profileData['organizations']=$eduvalue['name'];
						
					}
					if($eduvalue['type']=='work')
					{
						if(isset($r_profileData['name']))
							$r_profileData['work']=$r_profileData['organizations'].", ".$data['name'];
						else
							$r_profileData['work']=$data['name'];
						
					}
			
			}
		}
		
		if(isset($data['image']))
		{
			$r_profileData['image']=$data['image']['url'];
		}
		if(isset($data['name']))
		{
			$namearr=explode(',',$data['name']);
			
			if(count==1)
			{
				$r_profileData['firstname']=$namearr[0];
			
			}
			if(count==2)
			{
				$r_profileData['firstname']=$namearr[0];
				$r_profileData['lastname']=$namearr[1];
			
			}
			
			
			if(count==3)
			{
				$r_profileData['firstname']=$namearr[0];
				$r_profileData['middlename']=$namearr[1];
				$r_profileData['lastname']=$namearr[2];
			
			}
			
		}
		
		foreach($gpfields as $key=>$arrkey)
		{

			if(is_array($profileData[$arrkey]))
			{
				
				$currentval=$this->populatearray($profileData[$arrkey]);				
				$r_profileData[$arrkey]=$currentval;
			}
			else
			{
			
				if($arrkey=="gender" )
				$r_profileData[$arrkey]=ucwords($profileData[$arrkey]);
				else								
				$r_profileData[$arrkey]=$profileData[$arrkey];
				
			
			}
		
		}
		
		
		return $r_profileData;

	}
	
	public function populatearray($profileData1)			
	{

			$count=0;						
		  foreach ($profileData1 as $key=>$value)
		  {
		 
				  	if(is_array($value))
				    {
								
				    		$returnval=$this->populatearray($value);
				        if($returnval)
				           return $returnval;
				    }
				    else if($key=='name')
				    {
								return $value;
				    }
		     
		      
		  }

		 
	}

}





