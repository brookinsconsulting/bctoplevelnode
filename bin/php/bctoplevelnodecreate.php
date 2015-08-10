#!/usr/bin/env php
<?php
/**
 * File containing a script to create a node in the top level root node
 *
 * @copyright Copyright (C) 1999 - 2011 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU GPL v2 (or later)
 * @version //autogentag//
 * @package bctoplevelnode
 */
 
// Load existing class autoloads
require('autoload.php');

// Load cli and script environment
$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "bctoplevelnodecreate : Script to create node in top level root node\n" ),
                                     'use-session' => true,
                                     'use-modules' => true,
                                     'use-extensions' => true ) ); 
$script->startup();

// Fetch default script options
$options = $script->getOptions( "[admin-user:][class-id:][name:]",
                                "",
                                array( 'admin-user' => 'Admin user login name',
                                       'class-id' => 'ClassID of node to create',
                                       'name' => 'Name of node to create' ) );
 
$script->initialize();

// Script parameters
$adminUser = isset( $options['admin-user'] ) ? $options['admin-user'] : 'admin';
$classID = isset( $options['class-id'] ) ? $options['class-id'] : 1;
$name = isset( $options['name'] ) ? $options['name'] : 'Default node name';

// Login as admin-user parameter 
$user = eZUser::fetchByName( $adminUser );
$userCreatorID = $user->attribute( 'contentobject_id' );

// Test for user object 
if( is_object( $user ) )
{
        if( $user->loginCurrent() )
        {
           $cli->output( 'Logged in as ' . "'" . $adminUser . "'" );
        }
}
else
{
    $cli->error( 'Not logged in as admin, script terminating!' );
    $script->shutdown( 1 );
}


// Create section
$section = new eZSection( array( 'name' => 'Content server', 'navigation_part_identifier' => 'ezcontentservernavigationpart' ) );
$section->store();

// Create object
$defaultSectionID = $section->ID;
$class = eZContentClass::fetch( $classID );
$contentObject = $class->instantiate( $userCreatorID, $defaultSectionID );

// Set remote_id content
$remoteID = "contentserver:incomingnode";
$contentObject->setAttribute( 'remote_id', $remoteID );
$contentObject->store();

// Fetch related IDs
$contentObjectID = $contentObject->attribute( 'id' );
$userID = $contentObjectID;
 
// Create node assignment
$nodeAssignment = eZNodeAssignment::create( array( 'contentobject_id' => $contentObjectID,
                                                   'contentobject_version' => 1,
                                                   'parent_node' => 1,
                                                   'is_main' => 1 ) );
$nodeAssignment->store();

// Set version modified and status content 
$version = $contentObject->version( 1 );
$version->setAttribute( 'modified', time() );
$version->setAttribute( 'status', EZ_VERSION_STATUS_DRAFT );
$version->store();

// Fetch contentObject IDs 
$contentObjectID = $contentObject->attribute( 'id' );
$contentObjectAttributes = $version->contentObjectAttributes();

 // Set Name
$contentObjectAttributes[0]->setAttribute( 'data_text', $name );
$contentObjectAttributes[0]->store();

// Publish content object to top level root node
$operationResult = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $contentObjectID,'version' => 1 ) );

// Alert user to script completion
$cli->output( "Node created with object_id #$contentObjectID" );

// Exit script normally
$script->shutdown();
 
?>
