<?php
//
// Definition of eZEnum class
//
// Created on: <24-��-2002 16:07:05 wy>
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

/*! \file ezenum.php
*/

include_once( "lib/ezdb/classes/ezdb.php" );
include_once( "kernel/classes/ezpersistentobject.php" );
include_once( "kernel/classes/ezcontentclassattribute.php" );

/*!
  \class eZEnumValue ezenumvalue.php
  \ingroup eZDatatype
  \brief The class eZEnumValue does

*/

class eZEnumValue extends eZPersistentObject
{
    /*!
     Constructor
    */
    function eZEnumValue( $row )
    {
        $this->eZPersistentObject( $row );
    }

    function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "contentclass_attribute_id" => array( 'name' => "ContentClassAttributeID",
                                                                               'datatype' => 'integer',
                                                                               'default' => 0,
                                                                               'required' => true,
                                                                               'foreign_class' => 'eZContentObjectAttribute',
                                                                               'foreign_attribute' => 'id',
                                                                               'multiplicity' => '1..*' ),
                                         "contentclass_attribute_version" => array( 'name' => "ContentClassAttributeVersion",
                                                                                    'datatype' => 'integer',
                                                                                    'default' => 0,
                                                                                    'required' => true ),
                                         "enumelement" => array( 'name' => "EnumElement",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         "enumvalue" => array( 'name' => "EnumValue",
                                                               'datatype' => 'string',
                                                               'default' => '',
                                                               'required' => true ),
                                         "placement" => array( 'name' => "Placement",
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ) ),
                      "keys" => array( "id", "contentclass_attribute_id", "contentclass_attribute_version" ),
                      "increment_key" => "id",
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "eZEnumValue",
                      "name" => "ezenumvalue" );
    }

    function &clone()
    {
        $row = array( "id" => null,
                      "contentclass_attribute_id" => $this->attribute( 'contentclass_attribute_id' ),
                      "contentclass_attribute_version" => $this->attribute( 'contentclass_attribute_version' ),
                      "enumvalue" => $this->attribute( 'enumvalue' ),
                      "enumelement" => $this->attribute( 'enumelement' ),
                      "placement" => $this->attribute( 'placement' ) );
        $newEnumValue = new eZEnumValue( $row );
        return $newEnumValue;
    }

    function create( $contentClassAttributeID, $contentClassAttributeVersion, $element )
    {
        $row = array( "id" => null,
                      "contentclass_attribute_id" => $contentClassAttributeID,
                      "contentclass_attribute_version" => $contentClassAttributeVersion,
                      "enumvalue" => "",
                      "enumelement" => $element,
                      "placement" => eZPersistentObject::newObjectOrder( eZEnumValue::definition(),
                                                                         "placement",
                                                                         array( "contentclass_attribute_id" => $contentClassAttributeID,
                                                                                "contentclass_attribute_version" => $contentClassAttributeVersion ) ) );
        return new eZEnumValue( $row );
    }

    function createCopy( $id, $contentClassAttributeID, $contentClassAttributeVersion, $element, $value, $placement )
    {
        $row = array( "id" => $id,
                      "contentclass_attribute_id" => $contentClassAttributeID,
                      "contentclass_attribute_version" => $contentClassAttributeVersion,
                      "enumvalue" => $value,
                      "enumelement" => $element,
                      "placement" => $placement );
        return new eZEnumValue( $row );
    }

    function removeAllElements( $contentClassAttributeID, $version )
    {
        eZPersistentObject::removeObject( eZEnumValue::definition(),
                                          array( "contentclass_attribute_id" => $contentClassAttributeID,
                                                 "contentclass_attribute_version" => $version) );
    }

    function remove( $id , $version )
    {
        eZPersistentObject::removeObject( eZEnumValue::definition(),
                                          array( "id" => $id,
                                                 "contentclass_attribute_version" => $version) );
    }

    function fetch( $id, $version, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZEnumValue::definition(),
                                                null,
                                                array( "id" => $id,
                                                       "contentclass_attribute_version" => $version),
                                                $asObject );
    }

    function fetchAllElements( $classAttributeID, $version, $asObject = true )
    {
        if ( is_null( $classAttributeID ) )
            $elementList = array();
        else
            $elementList = eZPersistentObject::fetchObjectList( eZEnumValue::definition(),
                                                                null,
                                                                array( "contentclass_attribute_id" => $classAttributeID,
                                                                       "contentclass_attribute_version" => $version ),
                                                                null,
                                                                null,
                                                                $asObject );
        return $elementList;
    }

    var $ID;
    var $ContentClassAttributeID;
    var $ContentClassAttributeVersion;
    var $EnumElement;
    var $EnumValue;
    var $Placement;
}

?>
