<?php

namespace PhpOrient\Abstracts;

use PhpOrient\Client;

abstract class EmptyTestCase extends \PHPUnit_Framework_TestCase {
    use ClientTrait;

    /**
     * @var Client
     */
    protected $client;
    protected $thisTest;

    public function setUp(){
        $this->client = $this->createClient();
        $this->thisTest = microtime(true);
    }

    public function tearDown(){
        $resultTime = microtime(true) - $this->thisTest;
        echo " " . str_pad( $this->getName(false) , 41, " ", STR_PAD_RIGHT ). " - Did in " . $resultTime . " seconds.\n";
    }

}
