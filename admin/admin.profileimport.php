<?php
defined( '_JEXEC' ) or die( ';)' );
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'config'.DS.'config.php' ); 

if( $controller = JRequest::getWord('controller'))
	{
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if( file_exists($path))
			require_once $path;
		else
			$controller = '';
	}
// Create the controller
$classname    = 'profileimportController'.$controller;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
