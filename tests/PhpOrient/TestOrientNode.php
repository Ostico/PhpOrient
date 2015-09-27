<?php
/**
 * User: gremorian
 * Date: 27/09/15
 * Time: 19.23
 *
 */

namespace PhpOrient;
use PhpOrient\Abstracts\EmptyTestCase;
use PhpOrient\Protocols\Binary\Data\Record;
use PhpOrient\Protocols\Binary\Serialization\CSV;
use PhpOrient\Protocols\Common\OrientNode;

class TestOrientNode  extends EmptyTestCase {

    public $stringCSVNodeList = <<<JSON
localName:"_hzInstance_1_orientdb",localId:"aa59628c-ee29-44c5-abd2-741bfe72f53a",members:[(id:"aa59628c-ee29-44c5-abd2-741bfe72f53a",name:"node1435528113524",startedOn:1435528705653t,listeners:[{"protocol":"ONetworkProtocolBinary","listen":"127.0.0.1:2424"},{"protocol":"ONetworkProtocolHttpDb","listen":"127.0.0.1:2480"}],databases:<>),(id:"4cef7910-023c-4bbb-a27e-bcda6444d7b4",name:"node1435528114865",startedOn:1435528707007t,listeners:[{"protocol":"ONetworkProtocolBinary","listen":"127.0.0.1:2425"},{"protocol":"ONetworkProtocolHttpDb","listen":"127.0.0.1:2481"}],databases:<>)]
JSON;


    public function testNodeInitialization(){

        $jsonNodes =  CSV::unserialize( $this->stringCSVNodeList );

        /**
         * @var $node Record
         */
        foreach( $jsonNodes['members'] as $node ){
            $orientNode = OrientNode::fromConfig( $node->getOData() );
            $this->assertInstanceOf( 'PhpOrient\Protocols\Common\OrientNode', $orientNode );
            $this->assertNotEmpty( $orientNode->id );
            $this->assertNotEmpty( $orientNode->name );
            $this->assertNotEmpty( $orientNode->host );
            $this->assertNotEmpty( $orientNode->port );
        }

    }

}
