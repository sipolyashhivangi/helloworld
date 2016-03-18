<?php

/* * ********************************************************************
 * Filename: QueueClient.php
 * Folder: components
 * Description: QueueClient Component class handles queue client
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../lib/php-amqplib/amqp.inc'));

class QueueClient extends CApplicationComponent {

    /**
     * 
     */
    public function send() {
        $exchange = 'router';
        $queue = 'msgs';
        $conn = new AMQPConnection('localhost', 5672, "guest", "guest");
        $ch = $conn->channel();

        /*
          The following code is the same both in the consumer and the producer.
          In this way we are sure we always have a queue to consume from and an
          exchange where to publish messages.
         */

        /*
          name: $queue
          passive: false
          durable: true // the queue will survive server restarts
          exclusive: false // the queue can be accessed in other channels
          auto_delete: false //the queue won't be deleted once the channel is closed.
         */
        $ch->queue_declare($queue, false, true, false, false);

        /*
          name: $exchange
          type: direct
          passive: false
          durable: true // the exchange will survive server restarts
          auto_delete: false //the exchange won't be deleted once the channel is closed.
         */

        $ch->exchange_declare($exchange, 'direct', false, true, false);

        $ch->queue_bind($queue, $exchange);

        $msg_body = "Hi From PHP";
        $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $ch->basic_publish($msg, $exchange);

        $ch->close();
        $conn->close();
        die;
    }

    public function receive() {
        $conn = new AMQPConnection('localhost', 5672, "guest", "guest");
        $channel = $conn->channel();
        $queue = "refreshAllQueue";
        $exchange = "router";
        $channel->queue_declare($queue);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange);

        $consumer = function($msg) {
                    echo ":Hello", $msg->body . "\n";
                    print_r($msg);
                    $msg->delivery_info["channel"]->basic_ack($msg->delivery_info['delivery_tag']);
                };
        $channel->basic_consume($queue, "", false, false, false, false, $consumer);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $conn->close();
        die;
    }

}

?>