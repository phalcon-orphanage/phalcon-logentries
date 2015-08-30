<?php

/*
 +------------------------------------------------------------------------+
 | Phalcon Framework                                                      |
 +------------------------------------------------------------------------+
 | Copyright (c) 2011-2015 Phalcon Team (http://www.phalconphp.com)       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Authors: Serghei Iakovlev <serghei@phalconphp.com>                     |
 +------------------------------------------------------------------------+
 */

namespace Phalcon\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\Exception;
use Phalcon\Logger\Formatter\Line as LineFormatter;

/**
 * Phalcon\Logger\Adapter\Logentries
 *
 * Adapter to store logs to Logentries
 *
 *<code>
 *  $logger = new \Phalcon\Logger\Adapter\Logentries(['token' => 'ad43g-dfd34-df3ed-3d3d3']);
 *  $logger->log("This is a message");
 *  $logger->log("This is an error", \Phalcon\Logger::ERROR);
 *  $logger->error("This is another error");
 *  $logger->close();
 *</code>
 *
 * @package Phalcon\Logger\Adapter
 */
class Logentries extends Adapter implements AdapterInterface
{
    /**
     * Logentries server address for receiving logs
     * @type string
     */
    const LE_ADDRESS = 'tcp://api.logentries.com';

    /**
     * Logentries server address for receiving logs via TLS
     * @type string
     */
    const LE_TLS_ADDRESS = 'tls://api.logentries.com';

    /**
     * Logentries server port for receiving logs by token
     * @type int
     */
    const LE_PORT = 10000;

    /**
     * Logentries server port for receiving logs with TLS by token
     * @type int
     */
    const LE_TLS_PORT = 20000;

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
     * Connection timeout
     * @var float
     */
    protected $connectionTimeout = 0.0;

    protected $errno = 0;
    protected $errstr = '';

    /**
     * Phalcon\Logger\Adapter\Logentries constructor
     *
     * @param array $options
     *
     * @throws \Phalcon\Logger\Exception
     */
    public function __construct(array $options = [])
    {
        $defaults = [
            'token'             => '',
            'datahub_enabled'   => false,
            'datahub_address'   => '',
            'datahub_port'      => self::LE_PORT,
            'host_name_enabled' => false,
            'host_name'         => '',
            'host_id'           => '',
            'persistent'        => true,
            'use_ssl'           => false, // possible problem here with ssl not sending
            'connection_timeout'=> ini_get('default_socket_timeout')
        ];

        $this->options = array_merge($defaults, $options);

        register_shutdown_function([$this, 'close']);

        try {
            if ($this->isDatahub()) {
                $this->validateDataHubIP($this->options['datahub_address']);

                // if datahub is being used the token should be set to null
                $this->options['token'] = null;
            } else {
                $this->validateToken($this->options['token']);
            }

            if ($this->isHostNameEnabled()) {
                if (empty($this->options['host_name'])) {
                    $this->options['host_name'] = 'host_name='.gethostname();
                } else {
                    $this->options['host_name'] = 'host_name=' . $this->options['host_name'];
                }
            }

            if (!empty($this->options['host_id'])) {
                $this->options['host_id'] = 'host_ID=' . $this->options['host_id'];
            }

            $this->connectionTimeout = (float) $this->options['connection_timeout'];
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
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

    public function isPersistent()
    {
        return (bool) $this->options['persistent'];
    }

    public function isTLS()
    {
        return (bool) $this->options['use_ssl'];
    }

    /**
     * Check if datahub is enabled
     *
     * @return bool
     */
    public function isDatahub()
    {
        return (bool) $this->options['datahub_enabled'];
    }

    public function isHostNameEnabled()
    {
        return (bool) $this->options['host_name_enabled'];
    }

    public function isConnected()
    {
        return is_resource($this->socket) && !feof($this->socket);
    }

    public function getPort()
    {
        if ($this->isTLS()) {
            return self::LE_TLS_PORT;
        } elseif ($this->isDatahub()) {
            return $this->options['datahub_port'];
        }

        return self::LE_PORT;
    }

    public function getAddress()
    {
        if ($this->isTLS() && !$this->isDatahub()) {
            return self::LE_TLS_ADDRESS;
        } elseif ($this->isDatahub()) {
            return $this->options['datahub_address'];
        }

        return self::LE_ADDRESS;
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
        if (!$this->connect()) {
            throw new Exception('Cannot send message to the Logentries because connection is invalid.');
        }

        $multiline = $this->substituteNewline($this->getFormatter()->format($message, $type, $timestamp, $context));

        $this->writeToSocket($multiline);
    }

    protected function connect()
    {
        if (!$this->isConnected()) {
            $this->createSocket();
        }

        return $this->isConnected();
    }

    /**
     * Check if a DataHub IP Address has been entered
     *
     * @param string $datahubIPAddress DataHub IP Address
     */
    protected function validateDataHubIP($datahubIPAddress)
    {
        if (empty($datahubIPAddress)) {
            trigger_error('Logentries Datahub IP Address was not provided', E_USER_ERROR);
        }
    }

    /**
     * Check if a Token has been entered
     *
     * @param string $token Token
     */
    public function validateToken($token)
    {
        if (empty($token)) {
            trigger_error('Logentries Token was not provided', E_USER_ERROR);
        }
    }

    protected function writeToSocket($line)
    {
        $line = rtrim($line, PHP_EOL). PHP_EOL;

        if ($this->isHostNameEnabled()) {
            $finalLine = $this->options['token'] .
                ' ' . $this->options['host_id'] .
                ' ' . $this->options['host_name'] .
                ' ' . $line;
        } else {
            $finalLine = $this->options['token'] . $this->options['host_id'] . ' ' . $line;
        }

        if ($this->isConnected()) {
            for ($written = 0; $written < strlen($finalLine); $written += $fwrite) {
                $fwrite = fwrite($this->socket, substr($finalLine, $written));
                if (false === $fwrite) {
                    return $written;
                }
            }

            return $written;
        }

        return 0;
    }

    protected function createSocket()
    {
        try {
            $port = $this->getPort();
            $address = $this->getAddress();

            if ($this->isPersistent()) {
                $resource = pfsockopen($address, $port, $this->errno, $this->errstr, $this->connectionTimeout);
            } else {
                $resource = fsockopen($address, $port, $this->errno, $this->errstr, $this->connectionTimeout);
            }

            if (!is_resource($resource)) {
                trigger_error(
                    sprintf(
                        "Couldn't create socket for Logentries Logger, reason: %s",
                        socket_strerror(socket_last_error($resource))
                    ),
                    E_USER_ERROR
                );
            }

            if (is_resource($resource) && !feof($resource)) {
                $this->socket = $resource;
            }
        } catch (Exception $e) {
            trigger_error("Error connecting to Logentries, reason: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    protected function substituteNewline($line)
    {
        $newLine = str_replace(PHP_EOL, chr(13), $line);

        return $newLine;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
        }
    }
}
