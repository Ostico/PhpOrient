<?php

namespace PhpOrient\Serialization;

use PhpOrient\Abstracts\EmptyTestCase;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Serialization\CSV;

class DecoderTest extends EmptyTestCase {

    public function testDeserializeNoClass() {
        $result = CSV::unserialize( 'foo:"bar"' );
        $this->assertEquals( [ 'foo' => 'bar' ], $result );
    }

    public function testDeserializeWithClass() {
        $result = CSV::unserialize( 'MyClass@foo:"bar"' );
        $this->assertEquals( [ 'oClass' => 'MyClass', 'foo' => 'bar' ], $result );
    }

    public function testDeserializeMultipleFields() {
        $result = CSV::unserialize( 'foo1:"bar1",foo2: "bar2"' );
        $this->assertEquals( [ 'foo1' => 'bar1', 'foo2' => 'bar2' ], $result );
    }

    public function testDeserializeInteger() {
        $result = CSV::unserialize( 'foo:1' );
        $this->assertEquals( [ 'foo' => 1 ], $result );
    }

    public function testDeserializeRID() {
        $result = CSV::unserialize( 'foo:#12:10' );
        $this->assertEquals( [ 'foo' => new ID( 12, 10 ) ], $result );
    }

    public function testDeserializeArray() {
        $result = CSV::unserialize( 'foo:[1, 2, #12:10]' );
        $this->assertEquals( [ 'foo' => [ 1, 2, new ID( 12, 10 ) ] ], $result );
    }

    public function testDeserializeSet() {
        $result = CSV::unserialize( 'foo:<1, 2, #12:10>' );
        $this->assertEquals( [ 'foo' => [ 1, 2, new ID( 12, 10 ) ] ], $result );
    }

    public function testDeserializeMap() {
        $result = CSV::unserialize( 'foo:{a: 1, b:2, c: #12:10}' );
        $this->assertEquals( [ 'foo' => [ 'a' => 1, 'b' => 2, 'c' => new ID( 12, 10 ) ] ], $result );
    }

    public function testDeserializeEmbeddedRecord() {

        $result = CSV::unserialize( 'Test@attr1:"test",attr2:(TestInfo@subAttr1:"sub test",subAttr2:123)' );
        $payload = [ ];
        if ( isset( $result[ 'oClass' ] ) ) {
            $payload[ 'oClass' ] = $result[ 'oClass' ];
            unset( $result[ 'oClass' ] );
        }
        $payload[ 'oData' ] = $result;
        $result             = Record::fromConfig( $payload );

        $testRecord = Record::fromConfig(
            [
                'oClass' => 'Test',
                'oData'  =>
                    array(
                        'attr1' => 'test',
                        'attr2' =>
                            Record::fromConfig( [
                                'oClass'  => 'TestInfo',
                                'version' => 0,
                                'oData'   =>
                                    array(
                                        'subAttr1' => 'sub test',
                                        'subAttr2' => '123',
                                    ),
                            ] ),
                    ),
            ]
        );

        $this->assertEquals( $testRecord, $result );

    }

    /**
     * @Skip
     */
    public function testDeserializeEmbeddedMaps(){

//        $this->markTestSkipped();

        $x = 'V@"b":[{"xx":{"xxx":[1,2,"abc"]}}],"c":[{"yy":{"yyy":[3,4,"cde"]}}]';
        $result = CSV::unserialize( $x );

        $this->assertEquals(
            [
                "oClass" => 'V',
                'b'      => [
                    [
                        'xx' => [
                            'xxx' => [ 1, 2, 'abc' ]
                        ]
                    ]
                ],
                'c'      => [
                    [
                        'yy' => [
                            'yyy' => [ 3, 4, 'cde' ]
                        ]
                    ]
                ]
            ], $result
        );

    }

}