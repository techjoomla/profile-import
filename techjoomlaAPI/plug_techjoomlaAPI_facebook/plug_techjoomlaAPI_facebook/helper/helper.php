<?php

	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
class plug_techjoomlaAPI_facebookHelper
{ 	
	
function plug_techjoomlaAPI_facebookRender_profile($profileData)
  {
		
		$data = $profileData['profiledata'];
  	
		$r_profileData=array();		
		$fbfields=$profileData['mapping_field'];
		//$fbfieldsA=array('first_name','middle_name','last_name','name','gender','email','work','location','hometown','bio','picture-url');
		
		foreach($fbfields as $key=>$arrkey)
		{
			
			
			if(is_array($data[$arrkey]))
			{
				
				$currentval=$this->populatearray($data[$arrkey]);				
				$r_profileData[$arrkey]=$currentval;
			}
			else
			{
				
				if($arrkey=="gender" )
				$r_profileData[$arrkey]=ucwords($data[$arrkey]);
				else								
				$r_profileData[$arrkey]=$data[$arrkey];
				
			
			}
		
		}
		if(isset($data['picture-url']))
		{
			$r_profileData['picture-url']=$data['picture-url'];
			
		}
		if(isset($data['birthday']))
		{
			$r_profileData['birthday']=str_replace('/','-',$data['birthday']);
			
		}
		
		$r_profileData['education']='';
		
		if(isset($data['education']))
		{
			$r_profileData['education']=$this->rendereducations($data['education']);
			
		}
		
		$r_profileData['graduation']=$r_profileData['education'];
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
	
	public function rendereducations($education)
	{
		$r_education='';
		foreach($education as $edukey=>$eduvalue)
			{
				if(trim($eduvalue['degree']['name']))
					{
						if(isset($r_education))					
							$r_education=$r_education.", ".$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						else
							$r_education=$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						
					}
			
			}
			return $r_education;
	
	}

}





