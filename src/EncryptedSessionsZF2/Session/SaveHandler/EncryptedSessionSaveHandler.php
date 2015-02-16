<?php

namespace EncryptedSessionsZF2\Session\SaveHandler;

use Zend\Session\SaveHandler\SaveHandlerInterface;
use Zend\Filter\Encrypt;
use Zend\Filter\Decrypt;

/**
 * Encrypted session save handler
 */
class EncryptedSessionSaveHandler implements SaveHandlerInterface
{
    /**
     * @var SessionSaveHandler
     */
    protected $sessionSaveHandler;

    /**
     * @var $encryptFilter
     */
    protected $encryptFilter;

    /**
     * @var $decryptFilter
     */
    protected $decryptFilter;


    /**
     * Constructor
     *
     * @param SaveHandlerInterface $sessionSaveHandler
     * @param Encrypt $encryptFilter
     * @param Decrypt $decryptFilter
     */
    public function __construct(
        SaveHandlerInterface $sessionSaveHandler,
        Encrypt $encryptFilter,
        Decrypt $decryptFilter
    ) {
        $this->sessionSaveHandler = $sessionSaveHandler;
        $this->encryptFilter = $encryptFilter;
        $this->decryptFilter = $decryptFilter;
    }

    /**
     * Open Session - retrieve resources
     *
     * @param string $savePath
     * @param string $name
     */
    public function open($savePath, $name)
    {
        return $this->sessionSaveHandler->open($savePath, $name);
    }

    /**
     * Close Session - free resources
     *
     */
    public function close()
    {
        return $this->sessionSaveHandler->close();
    }

    /**
     * Read session data
     *
     * @param string $id
     *
     * @return mixed $data
     */
    public function read($id)
    {
        // Return the data from the cache
        $data = $this->sessionSaveHandler->read($id);

        // If there's no data, just return it (null)
        if (empty($data)) {
            return $data;
        }

        // Decrypt and return the data
        return $this->decryptFilter->filter($data);
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $data
     */
    public function write($id, $data)
    {
        if (empty($data)) {
            return $data;
        }

        $data = $this->encryptFilter->filter($data);

        // Pass the encrypted session data to the decorated save handler.
        return $this->sessionSaveHandler->write($id, $data);
    }

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id)
    {
        return $this->sessionSaveHandler->destroy($id);
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime)
    {
        return $this->sessionSaveHandler->gc($maxlifetime);
    }
}
