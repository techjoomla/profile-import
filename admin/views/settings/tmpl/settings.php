<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
$profileimport_config['reg_direct']='';
require(JPATH_SITE.DS."administrator".DS."components".DS."com_profileimport".DS."config".DS."config.php");

	if( isset($profileimport_config['api']) )
		$apis =	$profileimport_config['api'];
	else
		$apis = '';

$import = array(1=>JText::_('PF_JS'), 2=>JText::_('PF_CB') );//array(0=>JText::_('PF_JOOMLA'),1=>JText::_('PF_JS'), 2=>JText::_('PF_CB') );
					$options= array();
					foreach($import as $key=>$value) {
						 	 $options[] = JHTML::_('select.option', $key, $value);
		 					 }

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
	$allowedapi=array('Facebook','Googleplus','Linkedin','Twitter');
	echo "<form method='POST' name='adminForm' class='form-validate' action='index.php'>";
	$apiselect = array();
	foreach($this->apiplugin as $api)
	{
		
		$apiname = ucfirst(str_replace('plug_techjoomlaAPI_', '',$api->element));
		if(in_array($apiname,$allowedapi))
		$apiselect[] = JHTML::_('select.option',$api->element, $apiname);
	}
	
	

	?>
	<table border="0" width="100%" class="adminlist">
	<tr>
			
			<?php
					$value=-1;
					if($profileimport_config['reg_direct']==0)
						$value = 0;
					else if($profileimport_config['reg_direct']==1)
						$value = 1;
					else if($profileimport_config['reg_direct']==2)
						$value = 2;
			?>
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('REG_DIR_DESC'), JText::_('REG_DIR'), '', JText::_('REG_DIR'));?></td>
				<td class="setting-td">
					<?php echo $radiolist = JHTML::_('select.radiolist', $options, 'data[reg_direct]', 'class="inputbox fieldlist"  ', 'value', 'text', $value);?>
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
		
		
		<?php 
						$pftitle='';
						$pfdesc='';
						if(isset($profileimport_config['pi_title_frontend']))
						$pftitle=$profileimport_config['pi_title_frontend'];
						if(isset($profileimport_config['pi_details_frontend']))
						$pfdesc=$profileimport_config['pi_details_frontend'];
						if(trim($pftitle)=='') $pftitle=JText::_('SELECT_API');
						if(trim($pfdesc)=='')		$pfdesc=JText::_('SELECT_API_DES');
		?>
		
		<tr>
			
			<?php
					
			?>
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('PF_TITLE_DESC'), JText::_('PF_TITLE'), '', JText::_('PF_TITLE'));?></td>
				<td class="setting-td">
					<textarea rows="3" cols="20" name="data[pi_title_frontend]"><?php echo $pftitle ?></textarea>
				</td>
		</tr>		
		
		<tr>
			
			<?php
					
			?>
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('PF_DETAILS_DESC'), JText::_('PF_DETAILS'), '', JText::_('PF_DETAILS'));?></td>
				<td class="setting-td">
					<textarea rows="3" cols="20" name="data[pi_details_frontend]"><?php echo $pfdesc ?></textarea>
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
