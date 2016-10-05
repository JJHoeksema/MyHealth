<?php
	//get and activate the autoloader
    require_once("Bin".DIRECTORY_SEPARATOR."autoloader.php");

	// get an instance of App
	$app = DMF\App::getInstance();

	// register new module, with directory name test
	$app->registerModule("Main");
	$app->registerModule("Beheer");
    $app->registerModule("Api");
	// libs``````````````
	$app->registerModule("Auth");
	$app->registerModule("PDF");
	$app->registerModule("ErrorHandler");

	//or for an folder setup
	$offset = str_replace('/index.php', '',
					str_replace($_SERVER["DOCUMENT_ROOT"], '',
						implode('/', explode(DIRECTORY_SEPARATOR,__FILE__))));
	$host = $_SERVER['HTTP_HOST'] . $offset;
	$uri = str_replace($offset, '',  $_SERVER['REQUEST_URI']);
	// handle request
	$app->handleRequest($host, $uri);