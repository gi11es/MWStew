<?php
require_once "bootstrap.php";
include_once( 'includes/Zipper.php' );

// Helpers
$sanitizer = new MWStew\Sanitizer( $_POST );
$templating = new MWStew\Templating();
$builder = new MWStew\Builder( $sanitizer, $sanitizer->getParam( 'ext_name' ) );

/* Get sanitized parameters */
$params = array(
	"lowername" => $builder->getLowercaseExtName(),
	"name" => $builder->getExtName(),
	"author" => $sanitizer->getParam( 'ext_author' ),
	"version" => strval( $sanitizer->getParam( 'ext_version' ) || "0.0.0" ),
	"license" => $sanitizer->getParam( 'ext_license' ),
	"desc" => $sanitizer->getParam( 'ext_description' ),
	"url" => $sanitizer->getParam( 'ext_url' ),
);

/* Build the extension zip file */

// Extension file
$builder->addFile( $builder->getExtName() . ".php", $templating->render( 'extension.php', $params ) );
$builder->addFile( "extension.json", $templating->render( 'extension.json', $params ) );

// JS Development files
if ( $sanitizer->getParam( 'ext_dev_js' ) !== null ) {
	$builder->addFile( '.jscsrc', $templating->render( '.jscsrc' ) );
	$builder->addFile( '.jshintignore', $templating->render( '.jshintignore' ) );
	$builder->addFile( '.jshintrc', $templating->render( '.jshintrc' ) );
	$builder->addFile( 'Gruntfile.js', $templating->render( 'Gruntfile.js' ) );
	$builder->addFile( 'package.json', $templating->render( 'package.json', $params ) );
	$builder->addFile( 'modules/ext.' . $builder->getNormalizedName() . '.js', $templating->render( 'modules/ext.extension.js', $params ) );
	$builder->addFile( 'modules/ext.' . $builder->getNormalizedName() . '.css', $templating->render( 'modules/ext.extension.css', $params ) );
}

// PHP Development files
if ( $sanitizer->getParam( 'ext_dev_php' ) !== null ) {
	$builder->addFile( 'composer.json', $templating->render( 'composer.json' ) );
}

// Language file
$builder->addFile( 'i18n/en.json', $templating->render( 'i18n/en.json', $params ) );
$builder->addFile( 'i18n/qqq.json', $templating->render( 'i18n/qqq.json', $params ) );

// Send to download
$zip = new MWStew\Zipper( BASE_PATH . '/temp/', $builder->getNormalizedName() );
$zip->addFilesToZip( $builder->getFiles() );
$zip->download();
