<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_SITE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_profileimport'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();


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
?>

<h1 class="contentheading">											
		 <?php echo JText::_('PI_SETT');?>
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
											
												
													<div id="<?php echo 'api_'.$api_cnt.'div'?>"  >
														<form name="<?php echo $form_name ?>" id="<?php echo $form_name ?>" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
																 <?php echo JText::_('CLICK_API_IMG');?><a href="javascript:document.<?php echo $form_name ?>.submit();"><img height="50" width="50" src="<?php echo $img_path.$result[$i]['img_file_name'] ?>" /></a>
																	<input type="hidden" name="option" value="com_profileimport"/>
																	<input type="hidden" name="controller" value="import"/>
																	<input type="hidden" name="task" value="get_request_token"/>
																	<input type="hidden" name="api_used" value="<?php echo $result[$i]['api_used'] ?>"/>
																	
														</form>
													</div>
												<div style="clear:both"></div>

										
										
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
