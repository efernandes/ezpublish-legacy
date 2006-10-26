<?php
//
// Created on: <06-Jul-2003 15:52:54 amos>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.6.x
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

include_once( 'lib/ezutils/classes/ezini.php' );

/*!
 \return the current language used.
*/
function ezcurrentLanguage()
{
    include_once( 'lib/ezlocale/classes/ezlocale.php' );
    $locale =& eZLocale::instance();
    return $locale->translationCode();
}

/*!
 Replaces keys found in \a $text with values in \a $arguments.
 If \a $arguments is an associative array it will use the argument
 keys as replacement keys. If not it will convert the index to
 a key looking like %n, where n is a number between 1 and 9.
 Returns the new string.
*/
function &ezinsertarguments( $text, $arguments )
{
    if ( is_array( $arguments ) )
    {
        $replaceList = array();
        foreach ( $arguments as $argumentKey => $argumentItem )
        {
            if ( is_int( $argumentKey ) )
                $replaceList['%' . ( ($argumentKey%9) + 1 )] = $argumentItem;
            else
                $replaceList[$argumentKey] = $argumentItem;
        }
        $text = strtr( $text, $replaceList );
    }
    return $text;
}

/*!
 Translates the source \a $source with context \a $context and optional comment \a $comment
 and returns the translation.
 Uses eZTranslatorMananger::translate() to do the actual translation.

 If the site.ini settings RegionalSettings/TextTranslation is set to disabled this function
 will only return the source text.
*/
$ini =& eZINI::instance();
$useTextTranslation = false;
if ( $ini->variable( 'RegionalSettings', 'TextTranslation' ) != 'disabled' )
{
    $language =& ezcurrentLanguage();
    if ( file_exists( 'share/translations/' . $language . '/translation.ts' ) )
    {
        $useTextTranslation = true;
    }
    
    if ( $language != "eng-GB" ) // eng-GB does not need translation
    {
        include_once( 'lib/ezutils/classes/ezextension.php' );
        include_once( 'lib/ezi18n/classes/eztranslatormanager.php' );
        include_once( 'lib/ezi18n/classes/eztstranslator.php' );
    }
}

if ( $useTextTranslation )
{
    function &ezi18n( $context, $source, $comment = null, $arguments = null )
    {
        $text = eZTranslateText( $context, $source, $comment, $arguments );
        return $text;
    }

    function &ezx18n( $extension, $context, $source, $comment = null, $arguments = null )
    {
        $text = eZTranslateText( $context, $source, $comment, $arguments );
        return $text;
    }

    function &eZTranslateText( $context, $source, $comment = null, $arguments = null )
    {
        $language = ezcurrentLanguage();

        $file = 'translation.ts';

        // translation.ts translation
        $ini =& eZINI::instance();
        $useCache = $ini->variable( 'RegionalSettings', 'TranslationCache' ) != 'disabled';
        eZTSTranslator::initialize( $context, $language, $file, $useCache );

        // Bork translation: Makes it easy to see what is not translated.
        // If no translation is found in the eZTSTranslator, a Bork translation will be returned.
        // Bork is different than, but similar to, eng-GB, and is enclosed in square brackets [].
        $developmentMode = $ini->variable( 'RegionalSettings', 'DevelopmentMode' ) != 'disabled';
        if ( $developmentMode )
        {
            include_once( 'lib/ezi18n/classes/ezborktranslator.php' );
            eZBorkTranslator::initialize();
        }

        $man =& eZTranslatorManager::instance();
        $trans =& $man->translate( $context, $source, $comment );
        if ( $trans !== null ) {
            $text =& ezinsertarguments( $trans, $arguments );
            return $text;
        }

        eZDebug::writeWarning( "No translation for file(translation.ts) in context($context): '$source' with comment($comment)", "ezi18n" );
        $text =& ezinsertarguments( $source, $arguments );
        return $text;
    }
}
else
{
    function &ezi18n( $context, $source, $comment = null, $arguments = null )
    {
        return ezinsertarguments( $source, $arguments );
    }

    function &ezx18n( $extension, $context, $source, $comment = null, $arguments = null )
    {
        return ezinsertarguments( $source, $arguments );
    }
}

?>
