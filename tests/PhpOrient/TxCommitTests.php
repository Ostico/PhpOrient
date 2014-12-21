<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 06/12/14
 * Time: 19.14
 *
 */
namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Binary\Data\Record;

class TxCommitTests extends TestCase {

    protected $db_name = 'test_transaction';

    /**
     * @var Record
     */
    protected $first_rec;

    public function setUp(){
        parent::setUp();

        $rec2            = [ 'oClass' => 'V', 'oData' => [ 'alloggio' => 'albergo', 'lavoro' => 'ufficio', 'vacanza' => 'montagna' ] ];
        $rec             = Record::fromConfig( $rec2 );
        $this->first_rec = $this->client->execute( 'recordCreate', [
                        'cluster_id' => 9,
                        'record'     => $rec
                ]
        );

    }

    public function testUpdateRollback() {

        $tx = $this->client->getTransaction();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );

        $recUp = [ 'alloggio' => 'home', 'lavoro' => 'bazar', 'vacanza' => 'sea' ];
        $rec2 = new Record();
        $rec2->setOData( $recUp );
        $rec2->setOClass( 'V' );
        $updated = $this->client->execute( 'recordUpdate', [
                        'cluster_id'       => $this->first_rec->getRid()->cluster,
                        'cluster_position' => $this->first_rec->getRid()->position,
                        'record'           => $rec2
                ]
        );

        $tx->rollback();

        $load = $this->client->execute( 'recordLoad', [ 'rid' => $this->first_rec->getRid() ]);
        $this->assertInstanceOf( '\PhpOrient\Protocols\Binary\Data\Record', $load[0] );
        $this->assertEquals( (string)$this->first_rec->getRid(), (string)$load[0]->getRid() );
        $this->assertEquals( (string)$this->first_rec, (string)$load[0] );

    }

    public function testUpdate(){

//        $recUp = [ 'alloggio' => 'home', 'lavoro' => 'bazar', 'vacanza' => 'sea' ];
//        $rec2 = new Record();
//        $rec2->setOData( $recUp );
//        $rec2->setOClass( 'V' );
//        $updateCommand = $this->client->recordUpdate( [
//                        'cluster_id'       => $this->first_rec->getRid()->cluster,
//                        'cluster_position' => $this->first_rec->getRid()->position,
//                        'record'           => $rec2
//                ]
//        );
//        exit;


        $tx = $this->client->getTransaction();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );

        $recUp = [ 'alloggio' => 'albergo', 'lavoro' => 'ufficio', 'vacanza' => 'montagna' ];
        $rec2 = new Record();
        $rec2->setOData( $recUp );
//        $rec2->setOClass( 'V' );
        $updateCommand = $this->client->recordUpdate( [
                        'cluster_id'       => $this->first_rec->getRid()->cluster,
                        'cluster_position' => $this->first_rec->getRid()->position,
                        'record'           => $rec2,
                        'record_version'   => $this->first_rec->getVersion()
                ]
        );

        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\operations\RecordUpdate', $updateCommand );
        $tx->attach( $updateCommand );

        $result = $tx->commit();

    }

}