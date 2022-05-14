<?php

namespace App\Websockets\SocketHandler;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

abstract class BaseSocketHandler implements \Ratchet\WebSocket\MessageComponentInterface
{

    /**
     * @inheritDoc
     */
    function onOpen(ConnectionInterface $conn)
    {
        dd($conn);
        dump('On OPEN');
    }

    /**
     * @inheritDoc
     */
    function onClose(ConnectionInterface $conn)
    {
        dump('On CLOSE');
    }

    /**
     * @inheritDoc
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        dump('On Error');
    }
}
