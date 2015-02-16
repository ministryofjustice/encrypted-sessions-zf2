<?php

namespace EncryptedSessionsZF2\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Session\SessionManager;
use Zend\Session\Exception\RuntimeException;
use Zend\Session\SaveHandler\SaveHandlerInterface;

use EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler;
use EncryptedSessionsZF2\Session\SaveHandler\NullSessionSaveHandler;

/**
 * Factory used to instantiate an encrypted session save handler.
 */
class EncryptedSessionSaveHandlerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return EncryptedSessionSaveHandler
     * @throws RuntimeException if configuration is missing.
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('Config');

        if (!isset($config['encrypted_sessions_zf2'])) {
            throw new RuntimeException('Have you copied '.
             '"config/encrypted_sessions_zf2.local.php.dist" into your '.
             'project (without the .dist extension)?');
        }

        // To support encryption of Zend Framework's default Session Save Handler
        // (default PHP implementation), logic is added to wrap the default
        // SessionHandler object that implements ZF2's SaveHandlerInterface,
        // which it does natively.

        if (isset($config['encrypted_sessions_zf2']['session']['save_handler'])) {
            $sessionSaveHandler = $serviceManager->get($config['encrypted_sessions_zf2']['session']['save_handler']);
        } else {
            $sessionSaveHandler = new NullSessionSaveHandler();
        }

        $encryptFilter = $serviceManager->get($config['encrypted_sessions_zf2']['session']['encryption_handler']);
        $decryptFilter = $serviceManager->get($config['encrypted_sessions_zf2']['session']['decryption_handler']);

        return new EncryptedSessionSaveHandler($sessionSaveHandler, $encryptFilter, $decryptFilter);
    }
}
