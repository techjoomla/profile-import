<?php

defined('_JEXEC') or die( 'Restricted access' );


require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_profileimport'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();

$document->addStyleSheet(JURI::base().'components/com_profileimport/css/profileimport.css'); 	
$session =& JFactory::getSession();

$itemid = JRequest::getVar('Itemid', '','GET');
$session->set('itemid_session',$itemid);	
$u =& JURI::getInstance();
$currentMenu= $u->toString();
$session->set('currentMenu', $currentMenu);
$base=JURI::base();

$user=JFactory::getUser();
if(!$user->id){
	echo JText::_('PI_LOGIN');
	return false;
}

if($profileimport_config['pi_title_frontend'])
$pftitle=$profileimport_config['pi_title_frontend'];
if($profileimport_config['pi_details_frontend'])
$pfdesc=$profileimport_config['pi_details_frontend'];
if(trim($pftitle)=='') 	$pftitle=JText::_('PI_SETT');
if(trim($pfdesc)=='')		$pfdesc=JText::_('SELECT_API_DES');
?>
<script>
function prosubmit(formname)
{
	
	conf=confirm("<?php echo JText::_('PF_SURE_TO_IMPORT'); ?>");
	formnamestr=formname.toString();
	if(conf.toString()=='true')
	document.forms[formnamestr].submit();
	else
	return;
}
</script>
<?php

		//newly added for JS toolbar inclusion
		if(JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_community') )
		{	
			require_once(JPATH_SITE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');				
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'toolbar.php');		
			$toolbar    =& CFactory::getToolbar();	
			$tool = CToolbarLibrary::getInstance();
			
			?>
			<style>
			<!--
				#proimport-wrap ul { margin: 0;padding: 0;}
			-->
			</style>
			<div id="proimport-wrap">
				<?php
				echo $tool->getHTML();
		}
		//eoc for JS toolbar inclusion			

?>

<h1 class="contentheading">											
		 <?php echo $pftitle;?>
</h1>
<?php
$apidata=$this->apidata;
if(empty($apidata))
{
	echo JText::_('NO_API_PLUG');
}?>
<div class="invitex_content" id="invitex_content">
			<ul class="invitex_ul" style="list-style-type:none;">
			<li  name="invite_apis" >
<?php
for($i=0; $i<count($apidata); $i++)
{
	if(!isset( $apidata[$i]['error_message']) )
	{
		$getTokenURL = JRoute::_("index.php?option=com_profileimport&controller=import&task=getRequestToken&api=".$apidata[$i]['api_used']);
?>
		<div class="api_title"><?php echo $pfdesc;?></div>
		<div style="clear:both"></div>
				<?php 
						 if(!empty($apidata))
						{
							$result=$apidata;
							$img_path=JURI::root()."components/com_profileimport/images/";
							$api_cnt=1;
							$j=1;
							?>
							
							<?php
							for($i=0; $i<count($result); $i++)
							{  
									if(!isset($result[$i]['error_message']))
									{
										$j++;
									?>
									
											<?php $form_name=$result[$i]['name'].'_connect_form';?>
											
												
													<div    class="form_api" id="<?php echo 'api_'.$api_cnt.'div'?>" >
														<form name="<?php echo $form_name ?>" id="<?php echo $form_name ?>" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
																 <div onclick="prosubmit('<?php echo $form_name ?>');"><img	class="api_img" height="50" width="50" src="<?php echo $img_path.$result[$i]['img_file_name'] ?>" /></div>
																	<input type="hidden" name="option" value="com_profileimport"/>
																	<input type="hidden" name="controller" value="import"/>
																	<input type="hidden" name="task" value="get_request_token"/>
																	<input type="hidden" name="api_used" value="<?php echo $result[$i]['api_used'] ?>"/>
																	
														</form>
													</div>
											

										
										
							<?php		
									$apis_style='';
									$api_cnt++;	
										
								}//end if ?>
							
							<?php
						}//end for each?>


<?php 
		}
	}
}//end for
?>
</li>
					</ul>
				</div>
<!-- newly added for JS toolbar inclusion  -->	
<?php
if(JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_community') )
{ 
?>	
	</div>	<!-- end of proimport-wrap div -->
<?php
} 
?>	
<!-- eoc for JS toolbar inclusion	 -->
