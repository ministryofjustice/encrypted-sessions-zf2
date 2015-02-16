<?php

namespace EncryptedSessionsZF2\Test\Session\SaveHandler;

use \PHPUnit_Framework_TestCase;
use EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler;
use Zend\Filter\Encrypt;
use Zend\Filter\Decrypt;
use Zend\Session\SaveHandler\SaveHandlerInterface;

class EncryptedSessionSaveHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $mockSessionSaveHandler;
    protected $mockEncryptionFilter;
    protected $mockDecryptionFilter;
    protected $encryptedSessionSaveHandler;

    public function setUp()
    {
        parent::setUp();

        $this->mockSessionSaveHandler = $this->getMock(
            'Zend\Session\SaveHandler\SaveHandlerInterface'
        );

        $this->mockEncryptionFilter = $this->getMock('Zend\Filter\Encrypt');
        $this->mockDecryptionFilter = $this->getMock('Zend\Filter\Decrypt');

        $this->encryptedSessionSaveHandler = new EncryptedSessionSaveHandler(
            $this->mockSessionSaveHandler,
            $this->mockEncryptionFilter,
            $this->mockDecryptionFilter
        );
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::open
     */
    public function testEncryptedSessionSaveHandlerOpenIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('open')
            ->with($this->equalTo('testPath'), $this->equalTo('testName'))
            ->will($this->returnValue(true));

        $result = $this->encryptedSessionSaveHandler->open('testPath', 'testName');

        $this->assertTrue($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::close
     */
    public function testEncryptedSessionSaveHandlerCloseIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('close')
            ->will($this->returnValue(true));

        $result = $this->encryptedSessionSaveHandler->close();

        $this->assertTrue($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::read
     */
    public function testEncryptedSessionSaveHandlerReadIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('read')
            ->with($this->equalTo('testId'))
            ->will($this->returnValue('testData'));

        $this->mockDecryptionFilter->expects($this->once())
            ->method('filter')
            ->with($this->equalTo('testData'))
            ->will($this->returnValue('decryptedData'));

        $result = $this->encryptedSessionSaveHandler->read('testId');

        $this->assertEquals('decryptedData', $result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::read
     */
    public function testEncryptedSessionSaveHandlerEmptyReadReturnsEmpty()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('read')
            ->with($this->equalTo('testId'))
            ->will($this->returnValue(''));

        $this->mockDecryptionFilter->expects($this->never())
            ->method('filter');

        $result = $this->encryptedSessionSaveHandler->read('testId');

        $this->assertEmpty($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::read
     */
    public function testEncryptedSessionSaveHandlerFalseReadReturnsFalse()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('read')
            ->with($this->equalTo('testId'))
            ->will($this->returnValue(false));

        $this->mockDecryptionFilter->expects($this->never())
            ->method('filter');

        $result = $this->encryptedSessionSaveHandler->read('testId');

        $this->assertFalse($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::write
     */
    public function testEncryptedSessionSaveHandlerWriteIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('write')
            ->with($this->equalTo('testId'), $this->equalTo('encryptedData'))
            ->will($this->returnValue(true));

        $this->mockEncryptionFilter->expects($this->once())
            ->method('filter')
            ->with($this->equalTo('testData'))
            ->will($this->returnValue('encryptedData'));

        $result = $this->encryptedSessionSaveHandler->write('testId', 'testData');

        $this->assertTrue($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::write
     */
    public function testEncryptedSessionSaveHandlerEmptyWriteReturnsEmpty()
    {
        $this->mockSessionSaveHandler->expects($this->never())
            ->method('write');

        $this->mockEncryptionFilter->expects($this->never())
            ->method('filter');

        $result = $this->encryptedSessionSaveHandler->write('testId', '');

        $this->assertEmpty($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::write
     */
    public function testEncryptedSessionSaveHandlerEmptyWriteReturnsFalse()
    {
        $this->mockSessionSaveHandler->expects($this->never())
            ->method('write');

        $this->mockEncryptionFilter->expects($this->never())
            ->method('filter');

        $result = $this->encryptedSessionSaveHandler->write('testId', false);

        $this->assertFalse($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::destroy
     */
    public function testEncryptedSessionSaveHandlerDestroyIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('destroy')
            ->with($this->equalTo('testId'))
            ->will($this->returnValue(true));

        $result = $this->encryptedSessionSaveHandler->destroy('testId');

        $this->assertTrue($result);
    }

    /**
     * @covers EncryptedSessionsZF2\Session\SaveHandler\EncryptedSessionSaveHandler::gc
     */
    public function testEncryptedSessionSaveHandlerGarbageCollectionIsCalled()
    {
        $this->mockSessionSaveHandler->expects($this->once())
            ->method('gc')
            ->with($this->equalTo(123456789))
            ->will($this->returnValue(true));

        $result = $this->encryptedSessionSaveHandler->gc(123456789);

        $this->assertTrue($result);
    }
}
