<?php
/**
 * Created by PhpStorm.
 * User: ethan
 * Date: 16/02/2015
 * Time: 11:19
 */

namespace EncryptedSessionsZF2\Test\Session\SaveHandler;

use ReflectionClass;
use \PHPUnit_Framework_TestCase;
use Zend\ServiceManager\Config as ServiceConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Filter\Encrypt;
use Zend\Filter\Decrypt;
use EncryptedSessionsZF2\Module;

class EncryptedSessionSaveHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        parent::setUp();

        $module = new Module();
        $config = $module->getConfig();

        $serviceConfig  = new ServiceConfig($config['service_manager']);
        $this->serviceManager = new ServiceManager($serviceConfig);

        $this->serviceManager->setService(
            'TestEncryptionFilter',
            $this->getMock('Zend\Filter\Encrypt')
        );

        $this->serviceManager->setService(
            'TestDecryptionFilter',
            $this->getMock('Zend\Filter\Decrypt')
        );

    }

    public function testEncryptedSessionSaveHandlerFactoryCanFetchSaveHandlerFromServiceManager()
    {
        $config = array(
            'encrypted_sessions_zf2' => array(
                'session' => array(
                    'save_handler' => null,
                    'encryption_handler' => 'TestEncryptionFilter',
                    'decryption_handler' => 'TestDecryptionFilter',
                ),
            )
        );

        $this->serviceManager->setService(
            'Config',
            $config
        );

        $saveHandler = $this->serviceManager->get('EncryptedSessionsZF2\Session\SaveHandler\Encrypted');

        $this->assertInstanceOf('EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler', $saveHandler);
    }

    /**
     * @expectedException Zend\ServiceManager\Exception\ServiceNotCreatedException
     * @covers EncryptedSessionsZF2\Factory\EncryptedSessionSaveHandlerFactory::createService
     */
    public function testEncryptedSessionSaveHandlerFactoryExceptionThrownWhenSaveHandlerConfigurationDoesNotExist()
    {

        $this->serviceManager->setService('Config', array());

        $saveHandler = $this->serviceManager->get('EncryptedSessionsZF2\Session\SaveHandler\Encrypted');
    }

    /**
     * @covers EncryptedSessionsZF2\Factory\EncryptedSessionSaveHandlerFactory::createService
     */
    public function testEncryptedSessionSaveHandlerFactoryUsesSaveHandlerServiceWhenSpecified()
    {
        $config = array(
            'encrypted_sessions_zf2' => array(
                'session' => array(
                    'save_handler' => 'EncryptedSessionsZF2\Session\SaveHandler\Test',
                    'encryption_handler' => 'TestEncryptionFilter',
                    'decryption_handler' => 'TestDecryptionFilter',
                ),
            )
        );

        $this->serviceManager->setService(
            'Config',
            $config
        );

        $this->serviceManager->setService(
            'EncryptedSessionsZF2\Session\SaveHandler\Test',
            $this->getMock('Zend\Session\SaveHandler\SaveHandlerInterface')
        );

        $saveHandler = $this->serviceManager->get('EncryptedSessionsZF2\Session\SaveHandler\Encrypted');

        // Use reflection to check type of protected property.
        $reflect = new ReflectionClass('EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler');
        $saveHandlerProperty = $reflect->getProperty('sessionSaveHandler');
        $saveHandlerProperty->setAccessible(true);
        $saveHandlerType = get_class($saveHandlerProperty->getValue($saveHandler));

        $this->assertContains('Mock_SaveHandlerInterface', $saveHandlerType);
    }

    /**
     * @covers EncryptedSessionsZF2\Factory\EncryptedSessionSaveHandlerFactory::createService
     */
    public function testEncryptedSessionSaveHandlerFactoryUsesDefaultPHPSaveHandlerWhenNullSpecified()
    {
        $config = array(
            'encrypted_sessions_zf2' => array(
                'session' => array(
                    'save_handler' => null,
                    'encryption_handler' => 'TestEncryptionFilter',
                    'decryption_handler' => 'TestDecryptionFilter',
                ),
            )
        );

        $this->serviceManager->setService(
            'Config',
            $config
        );

        $saveHandler = $this->serviceManager->get('EncryptedSessionsZF2\Session\SaveHandler\Encrypted');

        // Use reflection to check type of protect property.
        $reflect = new ReflectionClass('EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler');
        $saveHandlerProperty = $reflect->getProperty('sessionSaveHandler');
        $saveHandlerProperty->setAccessible(true);
        $saveHandlerType = get_class($saveHandlerProperty->getValue($saveHandler));

        $this->assertEquals('EncryptedSessionsZF2\Session\SaveHandler\NullSessionSaveHandler', $saveHandlerType);
    }
}
