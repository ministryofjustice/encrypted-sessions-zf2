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
     * {@inheritdoc}
     */
    public function open($savePath, $name)
    {
        return $this->sessionSaveHandler->open($savePath, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return $this->sessionSaveHandler->close();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function destroy($id)
    {
        return $this->sessionSaveHandler->destroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return $this->sessionSaveHandler->gc($maxlifetime);
    }
}
