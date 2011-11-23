<?php

	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
class plug_techjoomlaAPI_facebookHelper
{ 	
	
function plug_techjoomlaAPI_facebookRender_profile($profileData)
  {
		
		$data = $profileData['profiledata'];
  	
		$r_profileData=array();
		$fbfieldsarr=explode("\n",$profileData['mapping_field']);
		
		foreach($fbfieldsarr as $fbfieldskey=>$fbfieldsval)
		{
			
			$currentvalarr=array();
			$currentvalarr=explode('=',$fbfieldsval);
			if((trim($currentvalarr[1])) && isset($currentvalarr[1]))
			$currentvalarrFinal[]=trim($currentvalarr[1]);
		}
		$fbfields=$currentvalarrFinal;
		//$fbfieldsA=array('first_name','middle_name','last_name','name','gender','email','work','location','hometown','bio','picture-url');
		
		if(isset($data['date-of-birth']))
		{
			if($data['date-of-birth']['month']<10)
			$data['date-of-birth']['month']='0'.$data['date-of-birth']['month'];

			if($data['date-of-birth']['day']<10)
			$data['date-of-birth']['day']='0'.$data['date-of-birth']['day'];

			$r_profileData['birthdate']=	$data['date-of-birth']['year'].'-'.$data['date-of-birth']['month'].'-'.$data['date-of-birth']['day'];
		}
		
		$r_profileData['education']='';
		if(isset($data['education']))
		{
			foreach($data['education'] as $edukey=>$eduvalue)
			{
				if(isset(trim($eduvalue['degree']['name'])))
					{
						if(isset($r_profileData['education']))					
							$r_profileData['education']=$r_profileData['education'].", ".$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						else
						$r_profileData['education']=$eduvalue['degree']['name']." ".$eduvalue['school']['name']."  \n";
						$r_profileData['graduation']=$eduvalue['year']['name'];
					}
			
			}
		}
		
		foreach($currentvalarrFinal as $key=>$arrkey)
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
		print_r($r_profileData);die;
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





