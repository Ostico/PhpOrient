<?php
/**
 * User: gremorian
 * Date: 08/11/15
 * Time: 17.25
 *
 */

namespace PhpOrient;


class ErrorHandling {

    public static function handle( $errno, $errstr, $errfile, $errline ) {
        $errorType = array(
            E_WARNING      => 'WARNING',
            E_NOTICE       => 'NOTICE',
            E_USER_WARNING => 'USER WARNING',
            E_USER_NOTICE  => 'USER NOTICE',
            E_STRICT       => 'STRICT NOTICE',
            E_DEPRECATED   => 'DEPRECATION NOTICE',
        );
        if ( !( error_reporting() & $errno ) ) {
            // This error code is not included in error_reporting
            return;
        }
        switch ( $errno ) {
            case E_USER_WARNING:
            case E_WARNING:
                echo "<b>WARNING</b> [$errno] $errstr in file $errfile at line $errline<br />\n";
                break;

            case E_USER_NOTICE:
            case E_NOTICE:
                echo "<b>NOTICE</b> [$errno] $errstr in file $errfile at line $errline<br />\n";
                break;

            default:
                echo "{$errorType[$errno]} : [$errno] $errstr in file $errfile at line $errline<br />\n";
                break;
        }

        /* Don't execute PHP internal error handler */

        return true;
    }

}