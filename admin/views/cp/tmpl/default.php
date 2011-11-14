<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pane');
$document =& JFactory::getDocument();	
$document->addStyleSheet(JURI::base().'components/com_profileimport/css/profileimport.css'); 
$pane =& JPane::getInstance('tabs', array('startOffset'=>0)); 
#$xml = new JSimpleXML(); 
$xml = JFactory::getXMLParser('Simple');

$currentversion = '';
$xml->loadFile(JPATH_SITE.'/administrator/components/com_profileimport/profileimport.xml');
if($xml->document)
foreach($xml->document->_children as $var)
	{
		if($var->_name=='version')
			$currentversion = $var->_data;
	}
?>
<script type="text/javascript">

	function vercheck()
	{
		callXML('<?php echo $currentversion; ?>');
		if(document.getElementById('NewVersion').innerHTML.length<220)
		{
			document.getElementById('NewVersion').style.display='inline';
		}
	}

	function callXML(currversion)
	{
		if (window.XMLHttpRequest)
			{
		 	 xhttp=new XMLHttpRequest();
			}
		else // Internet Explorer 5/6
			{
		 	xhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}

	xhttp.open("GET","<?php echo JURI::base(); ?>index.php?option=com_profileimport&task=getVersion",false);
	xhttp.send("");
	latestver=xhttp.responseText;

	if(latestver!=null)
	  {
		if(currversion == latestver)
		{
			document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#339F1D;">&nbsp;<?php echo JText::_("LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
		else
		{
			document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#FF0000;">&nbsp;<?php echo JText::_("LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
	  }
     }
</script>

<div id="cpanel" style="float: left; width: 100%;">
		<div id= "cp1" style="float: left; width: 60%;">
			<div style="float: left;">
				<div class="icon">
				<a href="index.php?option=com_profileimport&view=settings">
				<img src="<?php echo JURI::base()?>components/com_profileimport/images/process.png" alt="Settings"/>
				<span><?php echo JText::_("BC_SETTINGS");?></span>
				</a>
				</div>	
			</div>				
		</div>	
		<div id="cp2" class="cp2" style="float: left; width: 40%;padding-bottom: 10px; ">
		<?php
		echo $pane->startPane( 'pane' );
		echo $pane->startPanel( JText::_('BC_ABOUT'), 'panel1' );?>
		<h1 style="color:#0B55C4;"><?php echo JText::_('ABOUT1');?></h1>
		<h3><b><?php echo JText::_('ABOUT2');?></b></h3>
		<ol>
			<li><?php echo JText::_('ABOUT3');?></li>
			<li><?php echo JText::_('ABOUT4');?></li>
	</ol>
		<p><?php echo JText::_('ABOUT6');?></p>  
		<p><?php echo JText::_('ABOUT7');?></p> 
		<?php
		echo $pane->endPanel();		
		?>
		</div>
</div>
<table style="margin-bottom: 5px; width: 100%; border-top: thin solid #e5e5e5; table-layout: fixed;">
	<tbody>
		<tr>
			<td style="text-align: left; width: 33%;">
				<a href="http://techjoomla.com/index.php?option=com_billets&view=tickets&layout=form&Itemid=18" target="_blank"><?php echo JText::_("TECHJ_SUP"); ?></a> 
				<br />
				<a href="http://twitter.com/techjoomla" target="_blank"><?php echo JText::_("TJ_FOL_ON_TWIT"); ?></a>
				<br />
				<a href="http://www.facebook.com/techjoomla" target="_blank"><?php echo JText::_("TJ_FOL_ON_FB"); ?></a>
				<br />
				<a href="http://extensions.joomla.org/extensions/communication/instant-messaging/9344" target="_blank"><?php echo JText::_( "TJ_JED_FED" ); ?> </a>
			</td>	
			<td style="text-align: center; width: 50%;"><?php echo JText::_("TJ_PROD_INTRO" ); ?>
				<br />
				<?php echo JText::_("TJ_COPYRIGHT"); ?> 
				<br />
				<?php echo JText::_("TJ_VERSION").' '.$currentversion; ?> 
				<br />
				<span class="latestbutton" style="color: #0B55C4; cursor: pointer;" onclick="vercheck();"> <?php echo JText::_('TJ_CHECK_LATEST_VERSION');?></span> 
				<span id='NewVersion' style='padding-top: 5px; color: #000000; font-weight: bold; padding-left: 5px;'></span>
			</td>
			<td style="text-align: right; width: 33%;">
				<a href='http://techjoomla.com/' taget='_blank'> <img src="<?php echo JURI::base() ?>components/com_profileimport/images/techjoomla.png" alt="TechJoomla" style="vertical-align:text-top;"/></a>
			</td>
		</tr>
	</tbody>
</table>

