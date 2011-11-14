<?php

jimport( 'joomla.filesystem.folder' );
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');


$db = & JFactory::getDBO();
$condtion = array(0 => '\'community\'',0 => '\'techjoomlaAPI\'');
$condtionatype = join(',',$condtion);
if(JVERSION >= '1.6.0')
{
	$query = "SELECT element FROM #__extensions WHERE  folder in ($condtionatype)";
}
else
{
	$query = "SELECT element FROM #__plugins WHERE folder in ($condtionatype)";
}
$db->setQuery($query);
$status = $db->loadResultArray();

$install_status = new JObject();
$install_source = $this->parent->getPath('source');




//install techjoomlaAPI plugins 
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook');
if (!in_array("plug_techjoomlaAPI_facebook", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_facebook' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_facebook' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Facebook API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Facebook API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Facebook API plugin installed").'</span>'; 	
}

$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_linkedin');
if (!in_array("plug_techjoomlaAPI_linkedin", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_linkedin' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_linkedin' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Linkedin API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Linkedin API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Linkedin API plugin installed").'</span>'; 	
}

$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter');
if (!in_array("plug_techjoomlaAPI_twitter", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_twitter' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_twitter' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Twitter API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Twitter API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Twitter API plugin installed").'</span>'; 	
}


$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_googleplus');
if (!in_array("plug_techjoomlaAPI_twitter", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_googleplus' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_googleplus' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's GooglePlus API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's GooglePlus API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's GooglePlus API plugin installed").'</span>'; 	
}

function com_install()
{
	$errors = FALSE;
	$db = & JFactory::getDBO();
	
	//-- common images
	$img_OK = '<img src="images/publish_g.png" />';
	$img_WARN = '<img src="images/publish_y.png" />';
	$img_ERROR = '<img src="images/publish_r.png" />';
	$BR = '<br />';
	$destination = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_profileimport' . DS . 'config' . DS ;
	$configarraydata = getConfig($destination.'configdefault.php'); 
	$configarray = getformattedarray($configarraydata); 
	
	if(JFolder::exists($destination.'config.php'))
    {         		
		$oldconfig = getConfig($destination.'config.php');
		$result = array_merge($configarray, $oldconfig); 
		$newconfigarray = getformattedarray($result); 
		
		if(JFile::exists($destination.'config.php'))
		{
		  JFile::delete($destination.'config.php');
		}
		$newdata = '<?php $profileimport_config = array('.print_r($newconfigarray, true).') ?>';
		JFile::write($destination.'config.php',$newdata);
    }
    				
	else if(!JFile::exists($destination.'config.php'))
	{
		$data = '<?php $profileimport_config = array('.print_r($configarray, true).') ?>';
		JFile::write($destination.'config.php',$data);
	}
		
			
	JFile::delete($destination.'configdefault.php');
	
}
function getformattedarray($result){
	foreach($result as $k=>$v)
	{
		if(is_array($v))
		{
			$str = 'array(';
			foreach ($v as $kk => $vv)
			{
				$str1[]= "'{$kk}' => '" . $vv . "'";
			} 	
			$str.= implode(",", $str1);;
			$str .= ')';
			$final[] ="'{$k}' => " . $str ;
		}
		else
			$final[]= "'{$k}' => '{$v}'" ;
	}
					
	return $configarray = implode(",\n", $final);
} 
	function getConfig($filename)
	{
		include($filename);
		return $profileimport_config;
	}
