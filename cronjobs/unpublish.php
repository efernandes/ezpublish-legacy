<?php
/**
 * File containing the unpublish.php cronjob
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version //autogentag//
 * @package kernel
 */

/*! \file
*/



// Check for extension
require_once( 'kernel/common/ezincludefunctions.php' );
eZExtension::activateExtensions();
// Extension check end

$ini = eZINI::instance( 'content.ini' );
$unpublishClasses = $ini->variable( 'UnpublishSettings','ClassList' );

$rootNodeIDList = $ini->variable( 'UnpublishSettings','RootNodeList' );

$currentDate = time();

foreach( $rootNodeIDList as $nodeID )
{
    $rootNode = eZContentObjectTreeNode::fetch( $nodeID );

    $articleNodeArray = $rootNode->subTree( array( 'ClassFilterType' => 'include',
                                                    'ClassFilterArray' => $unpublishClasses ) );

    foreach ( $articleNodeArray as $articleNode )
    {
        $article = $articleNode->attribute( 'object' );
        $dataMap = $article->attribute( 'data_map' );

        $dateAttribute = $dataMap['unpublish_date'];

        if ( $dateAttribute === null )
            continue;

        $date = $dateAttribute->content();
        $articleRetractDate = $date->attribute( 'timestamp' );
        if ( $articleRetractDate > 0 && $articleRetractDate < $currentDate )
        {
            // Clean up content cache
            eZContentCacheManager::clearContentCacheIfNeeded( $article->attribute( 'id' ) );

            $article->removeThis( $articleNode->attribute( 'node_id' ) );
        }
    }
}


?>
