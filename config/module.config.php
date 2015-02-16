<?php

use Zend\Filter\Encrypt;
use Zend\Filter\Decrypt;

return array(
    'service_manager' => array(
        'factories' => array(
            'EncryptedSessionsZF2\Session\SaveHandler\Encrypted' =>
                'EncryptedSessionsZF2\Factory\EncryptedSessionSaveHandlerFactory',
        )
    ),
);
