<?php
// Created on: <27-Jul-2007 09:29:16 bjorn>
//
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: n.n.n
// BUILD VERSION: nnnnn
// COPYRIGHT NOTICE: Copyright (C) 1999-2007 eZ systems AS
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

/*! \file ezimportdbafile.php
*/

include_once( 'lib/ezutils/classes/ezcli.php' );
include_once( 'kernel/classes/ezscript.php' );
include_once( 'kernel/classes/ezdatatype.php' );

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "eZ publish datatype sql update\n\n" .
                                                         "Script can be runned as:\n" .
                                                         "bin/php/ezimportdbafile.php --datatype=\n\n" .
                                                         "Example: bin/php/ezimportdbafile.php --datatype=ezisbn" ),
                                      'use-session' => false,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[datatype:]", "",
                                array( 'datatype' => "The name of the datatype where the database should be updated." ) );
$script->initialize();
$dataTypeName = $options['datatype'];

if ( $dataTypeName === null )
{
    $cli->output( "Error: The option --datatype is required. Add --help for more information." );
}

$allowedDatatypes = eZDataType::allowedTypes();
if ( $dataTypeName !== null and
     in_array( $dataTypeName, $allowedDatatypes ) )
{
    // Inserting data from the dba-data files of the datatypes
    eZDataType::loadAndRegisterAllTypes();
    $registeredDataTypes = eZDataType::registeredDataTypes();

    if ( isset( $registeredDataTypes[$dataTypeName] ) )
    {
        $dataType = $registeredDataTypes[$dataTypeName];
        if ( $dataType->importDBDataFromDBAFile() )
        {
            $cli->output( "The database is updated for the datatype: " .
                          $cli->style( 'emphasize' ) . $dataType->DataTypeString . $cli->style( 'emphasize-end' ) . "\n" .
                          'dba-data is imported from the file: ' .
                          $cli->style( 'emphasize' ) . $dataType->getDBAFilePath() .  $cli->style( 'emphasize-end' ) );
        }
        else
        {
            $cli->error( "Failed importing datatype related data into database: \n" .
                          '  datatype - ' . $dataType->DataTypeString . ", \n" .
                          '  dba-data file - ' . $dataType->getDBAFilePath() );
        }
    }
    else
    {
        $cli->error( "Error: The datatype " . $dataTypeName . " does not exist." );
    }
}
else
{
    $cli->error( "Error: The datatype " . $dataTypeName . " is not registered." );
}
$script->shutdown();

?>
