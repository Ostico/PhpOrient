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
    protected $first_rec, $sec_rec;

    public function setUp(){
        parent::setUp();

        $rec2            = [ 'oClass' => 'V', 'oData' => [ 'alloggio' => 'albergo' ] ];
        $rec             = Record::fromConfig( $rec2 );
        $this->first_rec = $this->client->execute( 'recordCreate', [
                        'cluster_id' => 9,
                        'record'     => $rec
                ]
        );

        $rec3            = [ 'oClass' => 'V', 'oData' => [ 'alloggio' => 'house' ] ];
        $rec             = Record::fromConfig( $rec3 );
        $this->sec_rec   = $this->client->execute( 'recordCreate', [
                        'cluster_id' => 9,
                        'record'     => $rec
                ]
        );

    }

    public function testUpdateRollback() {

        $tx = $this->client->getTransactionStatement();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );

        $recUp = [ 'alloggio' => 'home' ];
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
        $this->assertEquals( $this->first_rec->getVersion(), $load[0]->getVersion() );

    }

    public function testUpdate(){

//        $recUp = [ 'alloggio' => 'home' ];
//        $rec2 = new Record();
//        $rec2->setOData( $recUp );
//        $rec2->setOClass( 'V' );
//        $updateCommand = $this->client->recordUpdate( [
//                        'cluster_id'       => $this->first_rec->getRid()->cluster,
//                        'cluster_position' => $this->first_rec->getRid()->position,
//                        'record'           => $rec2
//                ]
//        );


        $tx = $this->client->getTransactionStatement();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );

        $recUp = [ 'alloggio' => 'baita di montagna' ];
        $rec2 = new Record();
        $rec2->setOData( $recUp );
        $rec2->setOClass( 'V' );
        $updateCommand = $this->client->recordUpdate( [
                        'cluster_id'       => $this->first_rec->getRid()->cluster,
                        'cluster_position' => $this->first_rec->getRid()->position,
                        'record'           => $rec2,
                        'record_version'   => $this->first_rec->getVersion()
                ]
        );

        $createCommand = $this->client->recordCreate( [
                'cluster_id' => 9,
                'record'     => ( new Record() )
                                    ->setOData( [ 'alloggio' => 'bungalow' ] )
                                    ->setOClass( 'V' )
        ] );

        $deleteCommand = $this->client->recordDelete( [
            'rid'    => $this->sec_rec->getRid()
        ] );

        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\operations\RecordUpdate', $updateCommand );
        $tx->attach( $updateCommand );
        $tx->attach( $createCommand );
        $tx->attach( $deleteCommand );

        $result = $tx->commit();

        /**
         * @var Record $record
         */
        foreach ( $result as $record ){
            if( $record->getRid() == $this->first_rec->getRid() ){
                $this->assertEquals( $record->getOData(), [ 'alloggio' => 'baita di montagna' ] );
                $this->assertEquals( $record->getOClass(), $this->first_rec->getOClass() );
            } else {
                $this->assertEquals( $record->getOData(), [ 'alloggio' => 'bungalow' ] );
                $this->assertEquals( $record->getOClass(), 'V' );
            }
        }

    }

}