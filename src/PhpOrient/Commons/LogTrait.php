<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 03/12/14
 * Time: 1.38
 *
 */

namespace PhpOrient\Commons;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PhpOrient\Configuration\Constants;

trait LogTrait {

    /**
     * @var \Monolog\Logger
     */
    protected $_logger;

    public function getLogger() {

        if ( $this->_logger === null && Constants::LOGGING ) {
            $this->_logger = new Logger( get_class( $this ) );
            $this->_logger->pushHandler( new StreamHandler( STDOUT, Logger::DEBUG ) );
        }

        return $this;

    }

    /**
     * View any string as a hexDump.
     *
     * This is most commonly used to view binary data from streams
     * or sockets while debugging, but can be used to view any string
     * with non-viewable characters.
     *
     * @version     1.3.2
     * @author      Aidan Lister <aidan@php.net>
     * @author      Peter Waller <iridum@php.net>
     * @link        http://aidanlister.com/2004/04/viewing-binary-data-as-a-hexDump-in-php/
     *
     * @param       string  $data        The string to be dumped
     * @param       bool $htmlOutput  Set to false for non-HTML output
     * @param       bool    $uppercase   Set to true for uppercase hex
     *
     * @return string|null
     */
    protected function _hexDump($data, $htmlOutput = false, $uppercase = true ) {
        // Init
        $hexi = '';
        $ascii = '';
        $dump = ( $htmlOutput === true) ? '<pre>' : '';
        $offset = 0;
        $len = strlen ( $data );

        // Upper or lower case hexadecimal
        $x = ($uppercase === false) ? 'x' : 'X';

        // Iterate string
        for($i = $j = 0; $i < $len; $i ++) {
            // Convert to hexadecimal
            $hexi .= sprintf ( "%02$x ", ord ( $data [$i] ) );

            // Replace non-viewable bytes with '.'
            if (ord ( $data [$i] ) >= 32) {
                $ascii .= ( $htmlOutput === true) ? htmlentities ( $data [$i] ) : $data [$i];
            } else {
                $ascii .= '.';
            }

            // Add extra column spacing
            if ($j === 7) {
                $hexi .= ' ';
                $ascii .= ' ';
            }

            // Add row
            if (++ $j === 16 || $i === $len - 1) {
                // Join the hexi / ascii output
                $dump .= sprintf ( "%04$x  %-49s  %s", $offset, $hexi, $ascii );

                // Reset vars
                $hexi = $ascii = '';
                $offset += 16;
                $j = 0;

                // Add newline
                if ($i !== $len - 1) {
                    $dump .= "\n";
                }
            }
        }

        // Finish dump
        $dump .= $htmlOutput === true ? '</pre>' : '';
        $dump .= "\n";

        // Output method
        return $dump;

    }

    public function hexDump( $message ){
        if( Constants::LOGGING ){
            $_msg = self::_hexDump( $message );
            $this->_logger->debug( $_msg );
        }
    }

    /**
     * Adds a log record at the DEBUG level.
     * @param  string $message The log message
     */
    public function debug( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->debug( $message );
        }
    }

    /**
     * Adds a log record at the INFO level.
     * @param  string $message The log message
     */
    public function info( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->info( $message );
        }
    }

    /**
     * Adds a log record at the INFO level.
     * @param  string $message The log message
     */
    public function notice( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->notice( $message );
        }
    }

    /**
     * Adds a log record at the WARNING level.
     * @param  string $message The log message
     */
    public function warn( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->warn( $message );
        }
    }

    /**
     * Adds a log record at the WARNING level.
     * @param  string $message The log message
     */
    public function warning( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->warn( $message );
        }
    }

    /**
     * Adds a log record at the ERROR level.
     * @param  string $message The log message
     */
    public function err( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->err( $message );
        }
    }

    /**
     * Adds a log record at the ERROR level.
     * @param  string $message The log message
     */
    public function error( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->err( $message );
        }
    }

    /**
     * Adds a log record at the CRITICAL level.
     * @param  string $message The log message
     */
    public function crit( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->crit( $message );
        }
    }

    /**
     * Adds a log record at the CRITICAL level.
     * @param  string $message The log message
     */
    public function critical( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->crit( $message );
        }
    }

    /**
     * Adds a log record at the ALERT level.
     * @param  string $message The log message
     */
    public function alert( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->alert( $message );
        }
    }

    /**
     * Adds a log record at the EMERGENCY level.
     * @param  string $message The log message
     */
    public function emerg( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->emerg( $message );
        }
    }

    /**
     * Adds a log record at the EMERGENCY level.
     * @param  string $message The log message
     */
    public function emergency( $message ) {
        if( Constants::LOGGING ){
            $this->_logger->emerg( $message );
        }
    }

} 