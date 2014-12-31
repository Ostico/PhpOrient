<?php

namespace PhpOrient\Abstracts;

use PhpOrient\PhpOrient;

abstract class EmptyTestCase extends \PHPUnit_Framework_TestCase {
    use ClientTrait;

    /**
     * @var PhpOrient
     */
    protected $client;
    protected $thisTest;

    public function setUp(){
        $this->client = $this->createClient();
        $this->thisTest = microtime(true);
    }

    public function tearDown(){
        $resultTime = microtime(true) - $this->thisTest;
        echo " " . str_pad(
                    substr(
                        get_class($this),
                        strrpos( get_class($this), "\\" ) + 1
                    ) . "::" . $this->getName(false), 61, " ", STR_PAD_RIGHT
            ) . " - Did in " . $resultTime . " seconds.\n";
    }

}
