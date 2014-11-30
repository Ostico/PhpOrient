<?php

namespace PhpOrient;

class ClientTest extends TestCase {

    public function testOne() {
        $client = $this->createClient();
        $transport = $client->getTransport();
        $client->execute('connect');
        var_export( $transport );
    }

}
