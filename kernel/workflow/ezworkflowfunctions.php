<?php
/**
 * File containing the eZWorkflowFunctions class.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

/*! \file
*/

class eZWorkflowFunctions
{
    static function addGroup( $workflowID, $workflowVersion, $selectedGroup )
    {
        list ( $groupID, $groupName ) = explode( '/', $selectedGroup );
        $ingroup = eZWorkflowGroupLink::create( $workflowID, $workflowVersion, $groupID, $groupName );
        $ingroup->store();
        return true;
    }

    static function removeGroup( $workflowID, $workflowVersion, $selectedGroup )
    {
        $workflow = eZWorkflow::fetch( $workflowID );
        if ( !$workflow )
            return false;
        $groups = $workflow->attribute( 'ingroup_list' );
        foreach ( array_keys( $groups ) as $key )
        {
            if ( in_array( $groups[$key]->attribute( 'group_id' ), $selectedGroup ) )
            {
                unset( $groups[$key] );
            }
        }

        if ( count( $groups ) == 0 )
        {
            return false;
        }
        else
        {
            $db = eZDB::instance();
            $db->begin();
            foreach(  $selectedGroup as $group_id )
            {
                eZWorkflowGroupLink::removeByID( $workflowID, $workflowVersion, $group_id );
            }
            $db->commit();
        }
        return true;
    }
}

?>
