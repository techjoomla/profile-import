<?php 
 /**
	* @package JomSocial Network Suggest
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
	*/ 

	// Check to ensure this file is within the rest of the framework
	defined('JPATH_BASE') or die();
	jimport("joomla.html.parameter.element");	
	jimport('joomla.html.html');
	jimport('joomla.form.formfield');

	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_facebook', JPATH_ADMINISTRATOR);
	if(JVERSION>=1.6)
	{
			class JFormFieldPathapi extends JFormField
			{
				/**
				 * The form field type.
				 *
				 * @var		string
				 * @since	1.6
				 */
				public $type = 'Pathapi';

				/**
				 * Method to get the field input markup.
				 *
				 * TODO: Add access check.
				 *
				 * @return	string	The field input markup.
				 * @since	1.6
				 */
				 
				protected function getInput()
				{
					$FieldValue		=new FieldValue();
					if($this->id=='jform_params_pathapi_facebook')
						return '<a href="https://developers.facebook.com/apps" target="_blank">'.JText::_('API_KEY_PATH').'</a>';
		
					$firstinstall	=$FieldValue->checkfirstinstall();
			
			
			
					if($this->id=='jform_params_mapping_field_0'){ 	//joomla	
						if($firstinstall)
						$fieldvalue='';
						else
						{				
							$fieldname	=$FieldValue->getMappingValue(0);		
							$fieldvalue	=$FieldValue->RenderField($fieldname,0);
					
						}
						return '<textarea class="inputbox" rows="8" cols="25" id="jform_params_pathapi_mapping_field_0" name="jform[params][pathapi_mapping_field_0]">'.$fieldvalue.'</textarea>';
					}
					if($this->id=='jform_params_mapping_field_1'){	//jomsocial
						if($firstinstall)
							$fieldvalue	='';
						else
						{
							$fieldname=$FieldValue->getMappingValue(1);
							$fieldvalue=$FieldValue->RenderField($fieldname,1);
					
						}
						return '<textarea class="inputbox" rows="8" cols="25" id="jform_params_pathapi_mapping_field_1" name="jform[params][pathapi_mapping_field_1]">'.$fieldvalue.'</textarea>';
					}
		
					if($this->id=='jform_params_mapping_field_2'){	//CB
						if($firstinstall)
							$fieldvalue	='';
						else
						{
								$fieldname=$FieldValue->getMappingValue(2);
								$fieldvalue=$FieldValue->RenderField($fieldname,2);
						
						}
						return '<textarea class="inputbox" rows="8" cols="25" id="jform_params_pathapi_mapping_field_2" name="jform[params][pathapi_mapping_field_2]">'.$fieldvalue.'</textarea>';
					}
		
				}
		
		}
	}
	else
	{
	class JElementPathapi extends JElement
	{
		public $type = 'Pathapi';
		var $_name = 'pathapi';
		function fetchElement($name, $value, &$node, $control_name)
		{
				
			$FieldValue		=new FieldValue();
			if($name=='pathapi_facebook')
				return '<a href="https://developers.facebook.com/apps" target="_blank">'.JText::_('API_KEY_PATH').'</a>';
				$firstinstall	=$FieldValue->checkfirstinstall();
			
				if($name=='mapping_field_0'){ 	//joomla	
					
					if($firstinstall)
						$fieldvalue='';
					else
					{				
						$fieldname	=$FieldValue->getMappingValue(0);		
						$fieldvalue	=$FieldValue->RenderField($fieldname,0);				
						
					}
					
					return '<textarea id="paramsmapping_field_0" class="text_area" rows="8" cols="20" name="params[mapping_field_0]">'.$fieldvalue.'</textarea>';	
				
				}
				
				if($name=='mapping_field_1'){	//jomsocial
					if($firstinstall)
					$fieldvalue='';		
					else
					{
						$fieldname=$FieldValue->getMappingValue(1);
						$fieldvalue=$FieldValue->RenderField($fieldname,1);					
					}
					return '<textarea id="paramsmapping_field_1" class="text_area" rows="8" cols="20" name="params[mapping_field_1]">'.$fieldvalue.'</textarea>';	
				}
		
				if($name=='mapping_field_2'){	//CB
				
					if($firstinstall)
						$fieldvalue='';		
					else
					{
							$fieldname=$FieldValue->getMappingValue(2);
							$fieldvalue=$FieldValue->RenderField($fieldname,2);
							
					}
					return '<textarea id="paramsmapping_field_2" class="text_area" rows="8" cols="20" name="params[mapping_field_2]">'.$fieldvalue.'</textarea>';	
				}
		
			}//function
	}//class

	}

	class FieldValue
	{
		public function checkfirstinstall()
		{
			if(JVERSION>=1.6)
			{
				$plugin = JPluginHelper::getPlugin('techjoomlaAPI', 'plug_techjoomlaAPI_facebook');
				$pluginParams = new JRegistry();    
				$pluginParams->loadString($plugin->params);
				$mapping_field_0 = $pluginParams->get('mapping_field_0'); 
				$mapping_field_1 = $pluginParams->get('mapping_field_1'); 
				$mapping_field_2 = $pluginParams->get('mapping_field_2'); 
			}
			else
			{
				$plugin = &JPluginHelper::getPlugin('techjoomlaAPI', 'plug_techjoomlaAPI_facebook');
				$pluginParams = new JParameter($plugin->params);
				$mapping_field_0 = $pluginParams->get('mapping_field_0'); 
				$mapping_field_1 = $pluginParams->get('mapping_field_1'); 
				$mapping_field_2 = $pluginParams->get('mapping_field_2'); 
			}
			
			if(isset($mapping_field_0) or isset($mapping_field_1) or isset($mapping_field_2))
			return 1;
			else
			return 0;
 
		}
		
		public function getMappingValue($fieldcode)
		{
			require_once(JPATH_SITE.DS.'components'.DS.'com_profileimport'.DS.'helper.php');	
			$fieldnameA=comprofileimportHelper::getFieldNames($fieldcode);
			return	$fieldnameA;
		}
	
		public function RenderField($fieldnameR,$integration)
		{
			if($integration==0)
			{
				$renderedfield	=	FieldValue::RenderField_joomla($fieldnameR);
				return $renderedfield;
			}
			if($integration==1)
			{
				$renderedfield	=	FieldValue::RenderField_js($fieldnameR);
				return $renderedfield;
			}
			
			if($integration==2)
			{
				$renderedfield	=	FieldValue::RenderField_cb($fieldnameR);
				return $renderedfield;
			}
			
		}
		
		public function RenderField_joomla($fieldnamej)
		{
			if(JVERSION>=1.6)
			{
				
			}
			
		}
		
		public function RenderField_js($fieldnamejs)
		{
			$defaultvalue='';
			foreach($fieldnamejs as $key=>$value)
			{
				if($value=='FIELD_ABOUTME')
				$defaultvalue.=$value.'=designation'."\n";
				
				if($value=='FIELD_GENDER')
				$defaultvalue.=$value.'=gender'."\n";
				
				if($value=='FIELD_ADDRESS')
				$defaultvalue.=$value.'=address'."\n";
				
				if($value=='FIELD_BIRTHDATE')
				$defaultvalue.=$value.'=birthdate'."\n";
				
				
				if($value=='FIELD_COLLEGE')
				$defaultvalue.=$value.'=education'."\n";
				
				if($value=='FIELD_COUNTRY')
				$defaultvalue.=$value.'=country'."\n";
				
				
				if($value=='FIELD_GRADUATION')
				$defaultvalue.=$value.'=education'."\n";
				
				if($value=='FIELD_MOBILE')
				$defaultvalue.=$value.'=phone-number'."\n";
							

				
				}
				return $defaultvalue;
				
			}
		
		
		
		public function RenderField_cb($fieldnamecb)
		{
			$defaultvalue='';
			foreach($fieldnamecb as $key=>$value)
			{
				if($value=='firstname')
				$defaultvalue.=$value.'=first-name'."\n";
				
				if($value=='lastname')
				$defaultvalue.=$value.'=last-name'."\n";
				
				if($value=='formatname')
				$defaultvalue.=$value.'=name'."\n";
				
				if($value=='avatar')
				$defaultvalue.=$value.'=picture_url'."\n";
				
				}
				return $defaultvalue;
		
		
		}
	
	}
