<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class comprofileimportHelper
{
 	
/*
	This function Get joomla,jomsocial,CB Field Names Array
	For Joomla Pass $code=0,
	For jomsocial Pass $code=1,
	For CB Pass $code=2,
*/
	public function getFieldNames($code){
		$fieldnames='';
		if($code==0)
		{
			$fieldnames0=comprofileimportHelper::getFieldNames_joomla();
			return $fieldnames0;
		}
			
		if($code==1)
		{
			$fieldnames1=comprofileimportHelper::getFieldNames_js();
			return $fieldnames1;
			
			
		}	
		if($code==2)
		{
			$fieldnames2=comprofileimportHelper::getFieldNames_cb();
			return $fieldnames2;
		}
	}

	public function getFieldNames_joomla()
	{

	}
	
	public function getFieldNames_js()
	{
		
		$db = JFactory::getDBO();

		// Get the user groups from the database.
		$db->setQuery(
			"SELECT fieldcode " .
			" FROM #__community_fields WHERE fieldcode<>''"
		);
		$options = $db->loadResultArray();
		return $options;
	}
	
	function getFieldNames_cb()
	{
			$db = JFactory::getDBO();

		// Get the user groups from the database.
		$db->setQuery(
			"SELECT name " .
			" FROM #__comprofiler_fields WHERE `table`='#__comprofiler'"
		);
		$options = $db->loadResultArray();
		return $options;
	}

}
//this class is used to make log for f/l/t controllers 
if (!class_exists('techjoomlaHelperLogs'))
{
class techjoomlaHelperLogs
{	
	function simpleLog($comment,$userid='',$type,$filename,$path="", $display=1,$params=array())
    {
    		 
    		if($path=="" and $type="plugin")
    		{
		  		if(JVERSION >='1.6.0')
					$path=JPATH_SITE.DS.'plugins'.DS.$params['group'].DS.$params['name'].DS.$params['name'].DS.'error_log';
					else
					$path=JPATH_SITE.DS.'plugins'.DS.$params['group'].DS.$params['name'].DS.'error_log';    		
    		}
    		
    		if($path=="" and $type="component")
    			$path=JPATH_JPATH_COMPONENT.DS.'error_log';  
    			   	
        // Include the library dependancies
        jimport('joomla.error.log');
        
        if($userid)
        $my = &JFactory::getUser($userid);
        else
        $my = &JFactory::getUser();
       
        
        $options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}\t{HTTP_CODE}\t{COMMENT}");
        
        if(isset($params['http_code']))
        $http_code=$params['http_code'];
        else
        $http_code='';
        
        if(isset($params['desc']))
        $desc=$params['desc'];
        else
        $desc='';
        
        
        // Create the instance of the log file in case we use it later
       	$log = &JLog::getInstance($filename, $options, $path);       	
        $log->addEntry(array('user' => $my->name .'('.$my->id.')','desc'=>$desc,'http_code'=>$http_code, 'comment' => $comment));
        
        if(isset($params['desc']) and $display==1)
        echo $my->name .'('.$my->id.')'.$comment.$params['desc']."]"."HTTP CODE:".$http_code."<BR>";   
                     
       	if(!isset($params['desc']))
      	JError::raiseWarning(500, $comment);
       

        
    }
    
    function xml2array($contents, $get_attributes=1, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
}  
}	
}


?>
