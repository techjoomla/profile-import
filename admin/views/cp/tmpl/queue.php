<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

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
	function checkforinverval(el)
	{
		if(el.value<3600){
			alert('".JText::_('INTERVAL_INV')." 3600 ".JText::_('BC_SECS')."'); 
			el.value = '';
			
		}
	}
";

	$document->addScriptDeclaration($js_key);	
?>
<form name="adminForm" method="post" id="queue" class="form-validate" action="">
	<div  style= "width:20%; float: left;">
		<fieldset class="queue">
			<legend><?php echo JText::_('PUSH_TO_Q') ?></legend>
			<table width="100%">
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPUSER'), JText::_('BC_USER'), '', JText::_('BC_USER'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="userid" value="" id="userid" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPSTATUS'), JText::_('BC_MSG'), '', JText::_('BC_MSG'));?></td>
					<td><textarea class="inputbox required" name="status" id="status" cols="25"></textarea></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPSELAPI'), JText::_('BC_SEL_API'), '', JText::_('BC_SEL_API'));?></td>
					<td><?php foreach($broadcast_config['api'] as $api){
					?>
						<span syle="vertical-align:text-top;"> 
						<input style="float:none;" type="checkbox" name="api_status[]" value="<?php echo $api; ?>" /><span><?php echo ucfirst(str_replace('plug_techjoomlaAPI_','', $api)); ?></span>
					</span>
					<?php 
					}
					?>
					</td>
				</tr>				
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPCOUNT'), JText::_('BC_COUNT'), '', JText::_('BC_COUNT'));?></td>
					<td><input type="text" class="inputbox required validate-numeric"  name="count" value="" id="" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPINTERVAL')." 3600 ".JText::_('BC_SECS'), JText::_('INTERVALS'), '', JText::_('INTERVALS'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="interval" value="" id="" size="30" OnChange= checkforinverval(this); /></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div  style= "width:77%; float: right;">
		<fieldset class="queue">
			<legend><?php echo JText::_('QUEUE_FORM_MESSAGE') ?></legend>
			<table class="adminlist" width="100%">
			<thead>
				<tr>
				<th><?php echo JText::_('BC_ID');?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC__MSG'), JText::_('BC_MSG'), '', JText::_('BC_MSG'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_USER'), JText::_('BC_USER'), '', JText::_('BC_USER'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_LAS_DATE'), JText::_('BC_LAS_DATE'), '', JText::_('BC_LAS_DATE'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_PEN_CNT'), JText::_('BC_PEN_CNT'), '', JText::_('BC_PEN_CNT'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_INT_TIM'), JText::_('BC_INT_TIM'), '', JText::_('BC_INT_TIM'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_PEN_API'), JText::_('BC_PEN_API'), '', JText::_('BC_PEN_API'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('DESC_BC_SUPPLIER'), JText::_('BC_SUPPLIER'), '', JText::_('BC_SUPPLIER'));?></th>
				</tr>
			</thead>
		<?php
			foreach($this->queues as $queue){
		?>
			<tr>
				<td align="center"><?php echo $queue->id;?></td>
				<td align="center"><?php echo $queue->status;?></td>
				<td align="center"><?php echo JFactory::getUser($queue->userid)->name;?></td>
				<td align="center"><?php echo $queue->date;?></td>
				<td align="center"><?php echo $queue->count;?></td>
				<td align="center"><?php echo $queue->interval;?></td>
				<td ><?php echo $queue->api;?></td>
				<td align="center"><?php echo $queue->supplier;?></td>
			</tr>
<?php } ?>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="com_broadcast" />		
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="cp" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>