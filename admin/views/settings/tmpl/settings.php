<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
$profileimport_config['reg_direct']='';
require(JPATH_SITE.DS."administrator".DS."components".DS."com_profileimport".DS."config".DS."config.php");
	$Joomla=$JomSocial=$CB='';
		if(isset($profileimport_config['reg_direct']))
		{
			if($profileimport_config['reg_direct'][0]	== 'Joomla') { $Joomla = ' selected ';}
			if($profileimport_config['reg_direct'][0]	== 'JomSocial') { $JomSocial = ' selected ';}
			if($profileimport_config['reg_direct'][0]	== 'Community Builder'){$CB = ' selected ';}
		}

	if( isset($profileimport_config['api']) )
		$apis =	$profileimport_config['api'];
	else
		$apis = '';
/* if($profileimport_config['show_status_update'])
		$show_status_update ='SELECTED=true';
	else
		$show_status_update_no ='SELECTED=true';
	if($profileimport_config['show_status_viarss'])
		$show_status_viarss ='SELECTED=true';
	else
		$show_status_viarss_no ='SELECTED=true';	*/

$document =& JFactory::getDocument();
if(JVERSION >= '1.6.0')
	$js_key="
	Joomla.submitbutton = function(task){ ";
else
	$js_key="
	function submitbutton( task ){";

	$js_key.="
		if (task == 'cancel')
		{";
	        if(JVERSION >= '1.6.0')
				$js_key.="Joomla.submitform(task);";
			else		
				$js_key.="document.adminForm.submit();";
	    $js_key.="
	    }else{	
			var validateflag = document.formvalidator.isValid(document.adminForm);
			if(validateflag){";
				if(JVERSION >= '1.6.0'){
					$js_key.="
				Joomla.submitform(task);";
				}else{		
					$js_key.="
				document.adminForm.submit();";
				}
			$js_key.="
			}else{
				return false;
			}
		}
	}
";

	$document->addScriptDeclaration($js_key);	
?>
<?php 
	echo "<form method='POST' name='adminForm' class='form-validate' action='index.php'>";
	$apiselect = array();
	foreach($this->apiplugin as $api)
	{
		$apiname = ucfirst(str_replace('plug_techjoomlaAPI_', '',$api->element));
		$apiselect[] = JHTML::_('select.option',$api->element, $apiname);
	}

	?>
	<table border="0" width="100%" class="adminlist">
	<tr>
							<td align="left" width="20%"><strong><span class="hasTip" title="<?php echo JText::_('REG_DIR_DESC') ?>"><?php echo JText::_('REG_DIR') ?>:</span></strong></td>
							<td ><select class="inputbox" name="data[reg_direct][]">
							<option <?php echo $Joomla ?>> Joomla </option>
							<?php 
				
							if(JFolder::exists($communityfolder)) { ?>
								<option <?php echo $JomSocial ?>> JomSocial </option>
							<?php } 	
				
							if(JFolder::exists($cbfolder)) { ?>
							<option <?php echo $CB ?>> Community Builder </option>
							<?php } ?>
				
							</select>
							</td>
			     </tr>		
		<tr>
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('SELECT_API_DES'), JText::_('SELECT_API'), '', JText::_('SELECT_API'));?></td>
			<td class="setting-td">
				<?php 
			if(!empty($apiselect))
				echo JHTML::_('select.genericlist', $apiselect, "data[api][]", ' multiple size="5"  ', "value", "text", $apis );
			else
				echo JText::_('NO_API_PLUG');
			?>
	
			</td>
		</tr>	
	</table>
	
<?php			
$option='com_profileimport';
?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />		
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="settings" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
