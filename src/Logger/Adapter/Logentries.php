<?php

namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\Exception;
use Phalcon\Logger\Formatter\Line as LineFormatter;

class Logentries extends Adapter implements AdapterInterface
{
    const STATUS_SOCKET_OPEN   = 1;
    const STATUS_SOCKET_FAILED = 2;
    const STATUS_SOCKET_CLOSED = 3;

    const LE_ADDRESS = 'api.logentries.com';
    const LE_PORT = 10000;

    /**
     * Name
     * @var string
     */
    protected $name;

    /**
     * Adapter options
     * @var array
     */
    protected $options = [];

    /**
     * Socket
     * @var resource
     */
    protected $socket = null;

    /**
     * Socket status
     * @var int
     */
    protected $socketStatus = self::STATUS_SOCKET_CLOSED;

    /**
     * Phalcon\Logger\Adapter\Logentries constructor
     *
     * @param string $name
     * @param array $options
     *
     * @throws \Phalcon\Logger\Exception
     */
    public function __construct($name = 'phalcon', $options = [])
    {
        $defaults = [
            'token'    => '',
            'use_tcp'  => true,
            'severity' => false
        ];

        if ($name) {
            $this->name = $name;
        }

        $this->options = array_merge($defaults, $options);

        if (empty($this->options['token'])) {
            throw new Exception('Logentries Token was not provided');
        }

        $this->createSocket();
    }

    /**
     * Setter for name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Phalcon\Logger\FormatterInterface
     */
    public function getFormatter()
    {
        if (!is_object($this->_formatter)) {
            $this->_formatter = new LineFormatter('%date% - %type% - %message%', 'Y-m-d G:i:s');
        }

        return $this->_formatter;
    }

    /**
     * Writes the log to the file itself
     *
     * @param string  $message
     * @param integer $type
     * @param integer $timestamp
     * @param array   $context
     *
     * @throws \Phalcon\Logger\Exception
     */
    public function logInternal($message, $type, $timestamp, array $context = [])
    {
        if (!is_resource($this->socket)) {
            throw new Exception("Cannot send message to the Logentries because connection is invalid.");
        }

        $line = $this->getFormatter()->format($message, $type, $timestamp, $context);

        $this->writeToSocket($line);
    }

    protected function writeToSocket($line)
    {
        if ($this->socketStatus == self::STATUS_SOCKET_OPEN) {
            $finalLine = $this->options['token'] . $line;
            socket_write($this->socket, $finalLine, strlen($finalLine));
        }
    }

    protected function createSocket()
    {
        try {
            if (true == $this->options['use_tcp']) {
                $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            } else {
                $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            }

            if (!is_resource($this->socket)) {
                trigger_error(
                    "Couldn't create socket for Logentries Logger, reason: " . socket_strerror(socket_last_error()),
                    E_USER_ERROR
                );
                $this->socketStatus = self::STATUS_SOCKET_FAILED;
            }

            $result = socket_connect($this->socket, self::LE_ADDRESS, self::LE_PORT);

            if (false == $result) {
                trigger_error(
                    "Couldn't connect to Logentries, reason: " . socket_strerror(socket_last_error()),
                    E_USER_ERROR
                );
                $this->socketStatus = self::STATUS_SOCKET_FAILED;
            }

            socket_set_nonblock($this->socket);
            $this->socketStatus = self::STATUS_SOCKET_OPEN;
        } catch (Exception $e) {
            trigger_error("Error connecting to Logentries, reason: " . $e->getMessage(), E_USER_ERROR);
            $this->socketStatus = self::STATUS_SOCKET_FAILED;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (is_resource($this->socket)) {
            socket_close($this->socket);
            $this->socketStatus = self::STATUS_SOCKET_CLOSED;
        }
    }
}
