#!/usr/bin/env php
<?php
/**
 * File containing a script to move a node to the top level root node
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
$options = $script->getOptions( "[admin-user:][node-id:]",
                                "",
                                array( 'admin-user' => 'Admin user login name',
                                       'node-id' => 'NodeID of content object to move to top level' ) );

$script->initialize();

// Script parameters
$adminUser = isset( $options['admin-user'] ) ? $options['admin-user'] : 'admin';
$nodeID = isset( $options['node-id'] ) ? $options['node-id'] : false;
$name = isset( $options['name'] ) ? $options['name'] : 'Default node name';
$topLevelNodeID = 1;

// Test for required parameters, exit if not provided
if( !isset( $options['node-id'] ) or $nodeID == false )
{
    $cli->error( 'No node_id script paramter provided. Exiting ...' );
    $script->shutdown( 1 );
}

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

// Fetch node
$node = eZContentObjectTreeNode::fetch( $nodeID );
$objectID = $node->attribute( 'contentobject_id' );

// Test for valid node object
if( is_object( $node ) )
{
    /**
     * Move node to top level
     */
    $operationalResult = eZOperationHandler::execute( 'content', 'move',
                                                      array( 'node_id' => $nodeID,
                                                             'object_id' => $objectID,
                                                             'new_parent_node_id' => $topLevelNodeID ),
                                                      null, true );
    if( !$operationalResult[ 'status' ] )
    {
        // Exit with an error message
    }

    /**
     * Clear node object cache
     */
    eZContentCacheManager::clearContentCacheIfNeeded( $objectID );

    // Alter user to results of script
    $cli->output( "Success: Node moved to top level: #$nodeID" );
}
else
{
    // Alter user to results of script
    $cli->output( "Node was -not- moved to top level: #$nodeID" );
}

// Exit script normally
$script->shutdown();

?>
