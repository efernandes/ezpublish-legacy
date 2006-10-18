<?php
//
// Definition of eZWorkflowEvent class
//
// Created on: <16-Apr-2002 11:08:14 amos>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.9.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
// 
// 
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

//!! eZKernel
//! The class eZWorkflowEvent does
/*!

*/

include_once( "lib/ezdb/classes/ezdb.php" );
include_once( "kernel/classes/ezpersistentobject.php" );
include_once( "kernel/classes/ezworkflowtype.php" );

class eZWorkflowEvent extends eZPersistentObject
{
    function eZWorkflowEvent( $row )
    {
        $this->eZPersistentObject( $row );
        $this->Content = null;
    }

    function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "version" => array( 'name' => "Version",
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         "workflow_id" => array( 'name' => "WorkflowID",
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true,
                                                                 'foreign_class' => 'eZWorkflow',
                                                                 'foreign_attribute' => 'id',
                                                                 'multiplicity' => '1..*' ),
                                         "workflow_type_string" => array( 'name' => "TypeString",
                                                                          'datatype' => 'string',
                                                                          'default' => '',
                                                                          'required' => true ),
                                         "description" => array( 'name' => "Description",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         "data_int1" => array( 'name' => "DataInt1",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int2" => array( 'name' => "DataInt2",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int3" => array( 'name' => "DataInt3",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_int4" => array( 'name' => "DataInt4",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         "data_text1" => array( 'name' => "DataText1",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text2" => array( 'name' => "DataText2",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text3" => array( 'name' => "DataText3",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "data_text4" => array( 'name' => "DataText4",
                                                                'datatype' => 'text',
                                                                'default' => '',
                                                                'required' => true ),
                                         "placement" => array( 'name' => "Placement",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ) ),
                      "keys" => array( "id", "version" ),
                      "function_attributes" => array( 'content' => 'content',
                                                      'workflow_type' => 'eventType' ),
                      "increment_key" => "id",
                      "sort" => array( "placement" => "asc" ),
                      "class_name" => "eZWorkflowEvent",
                      "name" => "ezworkflow_event" );
    }

    function create( $workflow_id, $type_string )
    {
        $row = array(
            "id" => null,
            "version" => 1,
            "workflow_id" => $workflow_id,
            "workflow_type_string" => $type_string,
            "description" => "",
            "placement" => eZPersistentObject::newObjectOrder( eZWorkflowEvent::definition(),
                                                               "placement",
                                                               array( "version" => 1,
                                                                      "workflow_id" => $workflow_id ) ) );
        return new eZWorkflowEvent( $row );
    }

    function fetch( $id, $asObject = true, $version = 0, $field_filters = null )
    {
        return eZPersistentObject::fetchObject( eZWorkflowEvent::definition(),
                                                $field_filters,
                                                array( "id" => $id,
                                                       "version" => $version ),
                                                $asObject );
    }

    function &fetchList( $asObject = true )
    {
        $objectList = eZPersistentObject::fetchObjectList( eZWorkflowEvent::definition(),
                                                            null, null, null, null,
                                                            $asObject );
        return $objectList;
    }

    function fetchFilteredList( $cond, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZWorkflowEvent::definition(),
                                                    null, $cond, null, null,
                                                    $asObject );
    }

    /*!
     Moves the object down if $down is true, otherwise up.
     If object is at either top or bottom it is wrapped around.
    */
    function move( $down, $params = null )
    {
        if ( is_array( $params ) )
        {
            $pos = $params["placement"];
            $wid = $params["workflow_id"];
            $version = $params["version"];
        }
        else
        {
            $pos = $this->Placement;
            $wid = $this->WorkflowID;
            $version = $this->Version;
        }
        eZPersistentObject::reorderObject( eZWorkflowEvent::definition(),
                                           array( "placement" => $pos ),
                                           array( "workflow_id" => $wid,
                                                  "version" => $version ),
                                           $down );
    }

    function attributes()
    {
        $eventType =& $this->eventType();
        return array_merge( eZPersistentObject::attributes(), $eventType->typeFunctionalAttributes() );
    }

    function hasAttribute( $attr )
    {
        $eventType =& $this->eventType();
        return eZPersistentObject::hasAttribute( $attr ) or
               in_array( $attr, $eventType->typeFunctionalAttributes() );
    }

    function &attribute( $attr )
    {
        $eventType =& $this->eventType();
        if ( is_object( $eventType ) and in_array( $attr, $eventType->typeFunctionalAttributes( ) ) )
        {
            $attributeDecoder =& $eventType->attributeDecoder( $this, $attr );
            return $attributeDecoder;
        }
        else
            return eZPersistentObject::attribute( $attr );
    }

    function &eventType()
    {
        if ( ! isset (  $this->EventType ) )
        {
            $this->EventType =& eZWorkflowType::createType( $this->TypeString );
        }
        return $this->EventType;
    }

    /*!
     Returns the content for this event.
    */
    function &content()
    {
        if ( $this->Content === null )
        {
            $eventType =& $this->eventType();
            $this->Content =& $eventType->workflowEventContent( $this );
        }

        return $this->Content;
    }

    /*!
     Sets the content for the current event
    */
    function setContent( $content )
    {
        $this->Content =& $content;
    }


    /*!
     Executes the custom HTTP action
    */
    function customHTTPAction( &$http, $action )
    {
        $eventType =& $this->eventType();
        $eventType->customWorkflowEventHTTPAction( $http, $action, $this );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    function store()
    {
        $db =& eZDB::instance();
        $db->begin();
        $stored = eZPersistentObject::store();

        $eventType =& $this->eventType();
        $eventType->storeEventData( $this, $this->attribute( 'version' ) );
        $db->commit();

        return $stored;
    }

    /// \privatesection
    var $ID;
    var $Version;
    var $WorkflowID;
    var $TypeString;
    var $Description;
    var $Placement;
    var $DataInt1;
    var $DataInt2;
    var $DataInt3;
    var $DataInt4;
    var $DataText1;
    var $DataText2;
    var $DataText3;
    var $DataText4;
    var $Content;
}

?>
