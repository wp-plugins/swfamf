<?php

	//This file is intentionally left blank so that you can add your own global settings
	//and includes which you may need inside your services. This is generally considered bad
	//practice, but it may be the only reasonable choice if you want to integrate with
	//frameworks that expect to be included as globals, for example TextPattern or WordPress

	//Set start time before loading framework
	error_reporting(E_ALL ^ E_DEPRECATED); 
	ini_set("display_errors", 0);
	
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);
	
	$servicesPath = "../";
	$voPath = "../objects/";
	
	
	
	//Wordpress integration in the gateway
	define('WP_USE_THEMES', false);
	require_once('../../../../../wp-blog-header.php');
	
	$echo = "wordpress base site: ".get_option("siteurl");
	define('GLOBAL_ECHO', $echo);
?>