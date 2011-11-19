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
					
					if($this->id=='jform_params_pathapi_facebook')
						return '<a href="https://developers.facebook.com/apps" target="_blank">'.JText::_('API_KEY_PATH').'</a>';
		
				} //function
				
			}
	}
	else
	{
		class JElementPathapi extends JElement
		{
		
				var $_name = 'pathapi';
				function fetchElement($name, $value, &$node, $control_name)
				{
					return '<a href="https://developers.facebook.com/apps" target="_blank">'.JText::_('API_KEY_PATH').'</a>';
				
				}//function
		}//class

	}


