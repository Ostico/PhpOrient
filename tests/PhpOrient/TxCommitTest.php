<?php
/**
 * User: Domenico Lupinetti ( Ostico )
 * Date: 06/12/14
 * Time: 19.14
 *
 */
namespace PhpOrient;

use PhpOrient\Abstracts\TestCase;
use PhpOrient\Protocols\Binary\Data\ID;
use PhpOrient\Protocols\Binary\Data\Record;

class TxCommitTest extends TestCase {

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

    public function testWrongOperation(){

        $tx = $this->client->getTransactionStatement();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $this->setExpectedException( '\PhpOrient\Exceptions\PhpOrientBadMethodCallException' );
        $tx->attach( $this->client->command( 'select from V limit 1' ) );

    }

    public function testUpdate(){

        $config = static::getConfig();
        $this->client = new PhpOrient();
        $this->client->configure( array(
            'username' => $config[ 'username' ],
            'password' => $config[ 'password' ],
            'hostname' => $config[ 'hostname' ],
            'port'     => $config[ 'port' ],
        ) );

        $this->client->dbOpen( $this->db_name, 'admin', 'admin' );

        $tx = $this->client->getTransactionStatement();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );
        $tx = $tx->begin();
        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\Transaction\TxCommit', $tx );

        $recUp = [ 'accommodation' => 'mountain cabin' ];
        $rec2 = new Record();
        $rec2->setOData( $recUp );
        $rec2->setOClass( 'V' );
        $rec2->setRid( $this->first_rec->getRid() );
        $rec2->setVersion( $this->first_rec->getVersion() );

        $updateCommand = $this->client->recordUpdate( $rec2 );

        //UPDATE AGAIN!!!
        $recUp = [ 'accommodation' => 'mountain cabin 2' ];
        $rec3 = new Record();
        $rec3->setOData( $recUp );
        $rec3->setOClass( 'V' );
        $rec3->setRid( $this->first_rec->getRid() );
        $rec3->setVersion( $this->first_rec->getVersion() );
        $updateCommand = $this->client->recordUpdate( $rec3 );


        $createCommand = $this->client->recordCreate(
            ( new Record() )
                ->setOData( [ 'accommodation' => 'bungalow' ] )
                ->setOClass( 'V' )
                ->setRid( new ID( 9 ) )
        );

        //CREATE ANOTHER ONE
        $createCommand2 = $this->client->recordCreate(
            ( new Record() )
                ->setOData( [ 'accommodation' => 'under the sky' ] )
                ->setOClass( 'V' )
                ->setRid( new ID( 9 ) )
        );

        $deleteCommand = $this->client->recordDelete( $this->sec_rec->getRid() );

        $this->assertInstanceOf( 'PhpOrient\Protocols\Binary\operations\RecordUpdate', $updateCommand );
        $tx->attach( $updateCommand );
        $tx->attach( $createCommand );
        $tx->attach( $createCommand2 );
        $tx->attach( $deleteCommand );

        $result = $tx->commit();

        /**
         * @var Record $record
         */
        foreach ( $result as $record ){
            if( $record->getRid() == $this->first_rec->getRid() ){
                $this->assertEquals( $record->getOData(), [ 'accommodation' => 'mountain cabin 2' ] );
                $this->assertEquals( $record->getOClass(), $this->first_rec->getOClass() );
            } elseif( $record->getRid()->__toString() == '#9:2' ) {
                $this->assertEquals( $record->getOData(), [ 'accommodation' => 'bungalow' ] );
                $this->assertEquals( $record->getOClass(), 'V' );
            } elseif( $record->getRid()->__toString() == '#9:3' ) {
                $this->assertEquals( $record->getOData(), [ 'accommodation' => 'under the sky' ] );
                $this->assertEquals( $record->getOClass(), 'V' );
            }
        }

        //check for deleted record
        $deleted = $this->client->recordLoad( $this->sec_rec->getRid() );
        $this->assertEmpty( $deleted );

    }

}