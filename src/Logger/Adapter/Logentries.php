<?php

namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\Formatter\Line as LineFormatter;

class Logentries extends Adapter implements AdapterInterface
{
    const STATUS_SOCKET_OPEN   = 1;
    const STATUS_SOCKET_FAILED = 2;
    const STATUS_SOCKET_CLOSED = 3;

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

    public function __construct($name = 'phalcon', $options = [])
    {
        $defaults = [
            'logger_name' => 'Default',
            'token'       => '',
            'use_tcp'     => true,
            'severity'    => false
        ];

        $this->name = $name;
        $this->options = array_merge($defaults, $options);
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
     * @return \Phalcon\Logger\Formatter\Line
     */
    public function getFormatter()
    {
        if (!is_object($this->_formatter)) {
            $this->_formatter = new LineFormatter($this->name);
        }

        return $this->_formatter;
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
