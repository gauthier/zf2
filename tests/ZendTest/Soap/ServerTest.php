<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Soap;

require_once __DIR__ . '/TestAsset/commontypes.php';

use Zend\Soap\AutoDiscover;
use Zend\Soap\Server;

/**
 * Zend_Soap_Server
 *
 * @group      Zend_Soap
 * @group      Zend_Soap_Server
 */
class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('soap')) {
           $this->markTestSkipped('SOAP Extension is not loaded');
        }
    }

    public function testSetOptions()
    {
        $server = new Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'classmap' => array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                                             'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',),
                         'encoding' => 'ISO-8859-1',
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testSetOptionsViaSecondConstructorArgument()
    {
        $options = array(
            'soap_version' => SOAP_1_1,
            'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
            'classmap' => array(
                'TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',
            ),
            'encoding' => 'ISO-8859-1',
            'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
        );
        $server = new Server(null, $options);

        $this->assertTrue($server->getOptions() == $options);
    }

    /**
     * @group ZF-9816
     */
    public function testSetOptionsWithFeaturesOption()
    {
        $server = new Server(null, array(
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS
        ));

        $this->assertEquals(
            SOAP_SINGLE_ELEMENT_ARRAYS,
            $server->getSoapFeatures()
        );
    }

    public function testSetWsdlViaOptionsArrayIsPossible()
    {
        $server = new Server();
        $server->setOptions(array('wsdl' => 'http://www.example.com/test.wsdl'));

        $this->assertEquals('http://www.example.com/test.wsdl', $server->getWSDL());
    }

    public function testGetOptions()
    {
        $server = new Server();

        $this->assertTrue($server->getOptions() == array('soap_version' => SOAP_1_2));

        $options = array('soap_version' => SOAP_1_1,
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $server->setOptions($options);

        $this->assertTrue($server->getOptions() == $options);
    }

    public function testEncoding()
    {
        $server = new Server();

        $this->assertNull($server->getEncoding());
        $server->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $server->getEncoding());

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid encoding specified');
        $server->setEncoding(array('UTF-8'));
    }

    public function testSoapVersion()
    {
        $server = new Server();

        $this->assertEquals(SOAP_1_2, $server->getSoapVersion());
        $server->setSoapVersion(SOAP_1_1);
        $this->assertEquals(SOAP_1_1, $server->getSoapVersion());

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid soap version specified');
        $server->setSoapVersion('bogus');
    }

    public function testValidateUrn()
    {
        $server = new Server();
        $this->assertTrue($server->validateUrn('http://framework.zend.com/'));
        $this->assertTrue($server->validateUrn('urn:soapHandler/GetOpt'));

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid URN');
        $server->validateUrn('bogosity');
    }

    public function testSetActor()
    {
        $server = new Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid URN');
        $server->setActor('bogus');
    }

    public function testGetActor()
    {
        $server = new Server();

        $this->assertNull($server->getActor());
        $server->setActor('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getActor());
    }

    public function testSetUri()
    {
        $server = new Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid URN');
        $server->setUri('bogus');
    }

    public function testGetUri()
    {
        $server = new Server();

        $this->assertNull($server->getUri());
        $server->setUri('http://framework.zend.com/');
        $this->assertEquals('http://framework.zend.com/', $server->getUri());
    }

    public function testSetClassmap()
    {
        $server = new Server();

        $classmap = array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                          'TestData2' => '\ZendTest\Soap\TestAsset\TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
    }

    public function testSetClassmapThrowsExceptionOnBogusStringParameter()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Classmap must be an array');
        $server->setClassmap('bogus');
    }

    public function testSetClassmapThrowsExceptionOnBogusArrayParameter()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid class in class map');
        $server->setClassmap(array('soapTypeName', 'bogusClassName'));
    }

    public function testGetClassmap()
    {
        $server = new Server();

        $classmap = array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                          'TestData2' => '\ZendTest\Soap\TestAsset\TestData2');

        $this->assertNull($server->getClassmap());
        $server->setClassmap($classmap);
        $this->assertTrue($classmap == $server->getClassmap());
    }

    public function testSetWsdl()
    {
        $server = new Server();

        $this->assertNull($server->getWSDL());
        $server->setWSDL(__DIR__.'/_files/wsdl_example.wsdl');
        $this->assertEquals(__DIR__.'/_files/wsdl_example.wsdl', $server->getWSDL());

        //$this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'foo');
        $server->setWSDL(__DIR__.'/_files/bogus.wsdl');
    }

    public function testGetWsdl()
    {
        $server = new Server();

        $this->assertNull($server->getWSDL());
        $server->setWSDL(__DIR__.'/_files/wsdl_example.wsdl');
        $this->assertEquals(__DIR__.'/_files/wsdl_example.wsdl', $server->getWSDL());
    }

    public function testAddFunction()
    {
        $server = new Server();

        // Correct function should pass
        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        // Array of correct functions should pass
        $functions = array('\ZendTest\Soap\TestAsset\TestFunc2',
                           '\ZendTest\Soap\TestAsset\TestFunc3',
                           '\ZendTest\Soap\TestAsset\TestFunc4');
        $server->addFunction($functions);

        $this->assertEquals(
            array_merge(array('\ZendTest\Soap\TestAsset\TestFunc'), $functions),
            $server->getFunctions()
        );
    }

    public function testAddBogusFunctionAsInteger()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid function specified');
        $server->addFunction(126);
    }

    public function testAddBogusFunctionsAsString()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid function specified');
        $server->addFunction('bogus_function');
    }

    public function testAddBogusFunctionsAsArray()
    {
        $server = new Server();

        $functions = array('\ZendTest\Soap\TestAsset\TestFunc5',
                            'bogus_function',
                            '\ZendTest\Soap\TestAsset\TestFunc6');
        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'One or more invalid functions specified in array');
        $server->addFunction($functions);
    }

    public function testAddAllFunctionsSoapConstant()
    {
        $server = new Server();

        // SOAP_FUNCTIONS_ALL as a value should pass
        $server->addFunction(SOAP_FUNCTIONS_ALL);
        $server->addFunction('substr');
        $this->assertEquals(array(SOAP_FUNCTIONS_ALL), $server->getFunctions());
    }

    public function testSetClass()
    {
        $server = new Server();

        // Correct class name should pass
        $r = $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        $this->assertSame($server, $r);
    }

    /**
     * @group PR-706
     */
    public function testSetClassWithObject()
    {
        $server = new Server();

        // Correct class name should pass
        $object = new \ZendTest\Soap\TestAsset\ServerTestClass();
        $r = $server->setClass($object);
        $this->assertSame($server, $r);
    }

    public function testSetClassTwiceThrowsException()
    {
        $server = new Server();
        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $this->setExpectedException(
            'Zend\Soap\Exception\InvalidArgumentException',
            'A class has already been registered with this soap server instance'
            );
        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
    }

    public function testSetClassWithArguments()
    {
        $server = new Server();

        // Correct class name should pass
        $r = $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass', null, 1, 2, 3, 4);
        $this->assertSame($server, $r);
    }

    public function testSetBogusClassWithIntegerName()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid class argument (integer)');
        $server->setClass(465);
    }

    public function testSetBogusClassWithUnknownClassName()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Class "Zend_Soap_Server_Test_BogusClass" does not exist');
        $server->setClass('Zend_Soap_Server_Test_BogusClass');
    }

    /**
     * @group ZF-4366
     */
    public function testSetObject()
    {
        $server = new Server();

        // Correct class name should pass
        $r = $server->setObject(new TestAsset\ServerTestClass());
        $this->assertSame($server, $r);
    }

    /**
     * @group ZF-4366
     */
    public function testSetObjectThrowsExceptionWithBadInput1()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid object argument (integer)');
        $server->setObject(465);
    }

    /**
     * @group ZF-4366
     */
    public function testSetObjectThrowsExceptionWithBadInput2()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid object argument (integer)');
        $int = 1;
        $server->setObject($int);
    }

    /**
     * @group ZF-4366
     */
    public function testSetObjectThrowsExceptionWithBadInput3()
    {
        $server = new Server();

        //$this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'foo');
        $server->setObject(new TestAsset\ServerTestClass());
    }

    public function testGetFunctions()
    {
        $server = new Server();

        $server->addFunction('\ZendTest\Soap\TestAsset\TestFunc');

        $functions  =  array('\ZendTest\Soap\TestAsset\TestFunc2',
                             '\ZendTest\Soap\TestAsset\TestFunc3',
                             '\ZendTest\Soap\TestAsset\TestFunc4');
        $server->addFunction($functions);

        $functions  =  array('\ZendTest\Soap\TestAsset\TestFunc3',
                             '\ZendTest\Soap\TestAsset\TestFunc5',
                             '\ZendTest\Soap\TestAsset\TestFunc6');
        $server->addFunction($functions);

        $allAddedFunctions = array(
            '\ZendTest\Soap\TestAsset\TestFunc',
            '\ZendTest\Soap\TestAsset\TestFunc2',
            '\ZendTest\Soap\TestAsset\TestFunc3',
            '\ZendTest\Soap\TestAsset\TestFunc4',
            '\ZendTest\Soap\TestAsset\TestFunc5',
            '\ZendTest\Soap\TestAsset\TestFunc6'
        );
        $this->assertTrue($server->getFunctions() == $allAddedFunctions);
    }

    public function testGetFunctionsWithClassAttached()
    {
        $server = new Server();
        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $this->assertEquals(
            array('testFunc1', 'testFunc2', 'testFunc3', 'testFunc4', 'testFunc5'),
            $server->getFunctions()
        );
    }

    public function testGetFunctionsWithObjectAttached()
    {
        $server = new Server();
        $server->setObject(new TestAsset\ServerTestClass());

        $this->assertEquals(
            array('testFunc1', 'testFunc2', 'testFunc3', 'testFunc4', 'testFunc5'),
            $server->getFunctions()
        );
    }

    public function testSetPersistence()
    {
        $server = new Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());

        $server->setPersistence(SOAP_PERSISTENCE_REQUEST);
        $this->assertEquals(SOAP_PERSISTENCE_REQUEST, $server->getPersistence());
    }

    public function testSetUnknownPersistenceStateThrowsException()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Invalid persistence mode specified');
        $server->setPersistence('bogus');
    }

    public function testGetPersistence()
    {
        $server = new Server();

        $this->assertNull($server->getPersistence());
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $this->assertEquals(SOAP_PERSISTENCE_SESSION, $server->getPersistence());
    }

    public function testGetLastRequest()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastRequest() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $request =
          '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>';

        $response = $server->handle($request);

        $this->assertEquals($request, $server->getLastRequest());
    }

    public function testSetReturnResponse()
    {
        $server = new Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());

        $server->setReturnResponse(false);
        $this->assertFalse($server->getReturnResponse());
    }

    public function testGetReturnResponse()
    {
        $server = new Server();

        $this->assertFalse($server->getReturnResponse());

        $server->setReturnResponse(true);
        $this->assertTrue($server->getReturnResponse());
    }

    public function testGetLastResponse()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testGetLastResponse() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server->handle($request);

        $this->assertEquals($expectedResponse, $server->getResponse());
    }

    public function testHandle()
    {
        if (!extension_loaded('soap')) {
            $this->markTestSkipped('Soap extension not loaded');
        }

        if (headers_sent()) {
            $this->markTestSkipped('Cannot run testHandle() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $localClient = new TestAsset\TestLocalSoapClient($server,
                                                         null,
                                                         array('location'=>'test://',
                                                               'uri'=>'http://framework.zend.com'));

        // Local SOAP client call automatically invokes handle method of the provided SOAP server
        $this->assertEquals('Hello World!', $localClient->testFunc2('World'));


        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $expectedResponse =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2Response>'
          .             '<return xsi:type="xsd:string">Hello World!</return>'
          .         '</ns1:testFunc2Response>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server1 = new Server();
        $server1->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));

        $server1->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        $server1->setReturnResponse(true);

        $this->assertEquals($expectedResponse, $server1->handle($request));
    }

    /**
     * @dataProvider dataProviderForRegisterFaultException
     *
     * @param string|array $exception
     */
    public function testRegisterFaultException($exception)
    {
        $server = new Server();

        $server->registerFaultException($exception);

        if (!is_array($exception)) {
            $this->assertContains($exception, $server->getFaultExceptions());
        } else {
            foreach ($exception as $row) {
                $this->assertContains($row, $server->getFaultExceptions());
            }
        }
    }

    /**
     * @dataProvider dataProviderForRegisterFaultException
     *
     * @param string|array $exception
     */
    public function testDeregisterFaultException($exception)
    {
        $server = new Server();

        $server->registerFaultException($exception);
        if (is_array($exception)) {
            $exception = array_shift($exception);
        }

        $this->assertTrue($server->deregisterFaultException($exception));

        $this->assertNotContains($exception, $server->getFaultExceptions());
    }

    /**
     * @dataProvider dataProviderForRegisterFaultException
     *
     * @param string|array $exception
     */
    public function testIsRegisteredAsFaultException($exception)
    {

        $server = new Server();
        $server->registerFaultException($exception);


        if (!is_array($exception)) {
            $this->assertTrue($server->isRegisteredAsFaultException($exception));
        } else {
            foreach ($exception as $row) {
                $this->assertTrue($server->isRegisteredAsFaultException($row));
            }
        }
    }

    /**
     * @return array
     */
    public function dataProviderForRegisterFaultException()
    {
        return array(
            array('Exception'),
            array('Zend\Soap\Exception\InvalidArgumentException'),
            array('InvalidArgumentException'),
            array('Zend\Server\Exception\RuntimeException'),
            array(array('Zend\Server\Exception\RuntimeException')),
            array(array('Zend\Server\Exception\RuntimeException', 'InvalidArgumentException')),
        );
    }

    public function testFaultWithTextMessage()
    {
        $server = new Server();
        $fault = $server->fault('FaultMessage!');

        $this->assertTrue($fault instanceof \SoapFault);
        $this->assertContains('FaultMessage!', $fault->getMessage());
    }

    public function testFaultWithUnregisteredException()
    {
        $server = new Server();
        $fault = $server->fault(new \Exception('MyException'));

        $this->assertTrue($fault instanceof \SoapFault);
        $this->assertContains('Unknown error', $fault->getMessage());
        $this->assertNotContains('MyException', $fault->getMessage());
    }

    public function testFaultWithRegisteredException()
    {
        $server = new Server();
        $server->registerFaultException('\Zend\Soap\Exception\RuntimeException');
        $server->registerFaultException('\Zend\Soap\Exception\InvalidArgumentException');
        $fault = $server->fault(new \Zend\Soap\Exception\RuntimeException('MyException'));
        $this->assertTrue($fault instanceof \SoapFault);
        $this->assertNotContains('Unknown error', $fault->getMessage());
        $this->assertContains('MyException', $fault->getMessage());
    }

    public function testFaultWithBogusInput()
    {
        $server = new Server();
        $fault = $server->fault(array('Here', 'There', 'Bogus'));

        $this->assertContains('Unknown error', $fault->getMessage());
    }

    /**
     * @group ZF-3958
     */
    public function testFaultWithIntegerFailureCodeDoesNotBreakClassSoapFault()
    {
        $server = new Server();
        $fault = $server->fault("FaultMessage!", 5000);

        $this->assertTrue($fault instanceof \SoapFault);
    }

    /**
     * @expectedException \SoapFault
     */
    public function testHandlePhpErrors()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run ' . __METHOD__ . '() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $wsdlFilename = __DIR__ . '/TestAsset/testHandlePhpErrors.wsdl';
        $autodiscover = new AutoDiscover();
        $autodiscover->setOperationBodyStyle(array(
            'use'           => 'literal',
        ));

        $autodiscover->setBindingStyle(array(
            'style'         => 'document',
            'transport'     => 'http://schemas.xmlsoap.org/soap/http'
        ));


        $autodiscover->setServiceName('ExampleService');
        $autodiscover->setUri('http://example.com');


        $autodiscover->setClass('\ZendTest\Soap\TestAsset\errorClass');

        $wsdl = $autodiscover->generate();
        $wsdl->dump($wsdlFilename);

        $server = new Server($wsdlFilename);

        $server->setClass('\ZendTest\Soap\TestAsset\errorClass');

        $client = new \Zend\Soap\Client\Local($server, $wsdlFilename);
        $client->triggerError();
        unlink($wsdlFilename);
    }

    public function testLoadFunctionsIsNotImplemented()
    {
        $server = new Server();

        $this->setExpectedException('Zend\Soap\Exception\RuntimeException', 'Unimplemented method');
        $server->loadFunctions("bogus");
    }

    public function testErrorHandlingOfSoapServerChangesToThrowingSoapFaultWhenInHandleMode()
    {
        if (headers_sent()) {
            $this->markTestSkipped('Cannot run ' . __METHOD__ . '() when headers have already been sent; enable output buffering to run this test');
            return;
        }

        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        // Requesting Method with enforced parameter without it.
        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc5 />'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');
        $response = $server->handle($request);

        $this->assertContains(
            '<SOAP-ENV:Fault><faultcode>Receiver</faultcode><faultstring>Test Message</faultstring></SOAP-ENV:Fault>',
            $response
        );
    }

    /**
     * @group ZF-5597
     */
    public function testServerAcceptsZendConfigObject()
    {
        $options = array('soap_version' => SOAP_1_1,
                         'actor' => 'http://framework.zend.com/Zend_Soap_ServerTest.php',
                         'classmap' => array('TestData1' => '\ZendTest\Soap\TestAsset\TestData1',
                                             'TestData2' => '\ZendTest\Soap\TestAsset\TestData2',),
                         'encoding' => 'ISO-8859-1',
                         'uri' => 'http://framework.zend.com/Zend_Soap_ServerTest.php'
                        );
        $config = new \Zend\Config\Config($options);

        $server = new Server();
        $server->setOptions($config);
        $this->assertEquals($options, $server->getOptions());
    }

    /**
     * @group ZF-5300
     */
    public function testSetAndGetFeatures()
    {
        $server = new Server();
        $this->assertNull($server->getSoapFeatures());
        $server->setSoapFeatures(100);
        $this->assertEquals(100, $server->getSoapFeatures());
        $options = $server->getOptions();
        $this->assertTrue(isset($options['features']));
        $this->assertEquals(100, $options['features']);
    }

    /**
     * @group ZF-5300
     */
    public function testSetAndGetWSDLCache()
    {
        $server = new Server();
        $this->assertNull($server->getWSDLCache());
        $server->setWSDLCache(100);
        $this->assertEquals(100, $server->getWSDLCache());
        $options = $server->getOptions();
        $this->assertTrue(isset($options['cache_wsdl']));
        $this->assertEquals(100, $options['cache_wsdl']);
    }

    /**
     * @group ZF-11411
     */
    public function testHandleUsesProperRequestParameter()
    {
        $server = new \ZendTest\Soap\TestAsset\MockServer();
        $r = $server->handle(new \DOMDocument('1.0', 'UTF-8'));
        $this->assertTrue(is_string($server->mockSoapServer->handle[0]));
    }

    /**
     * @runInSeparateProcess
     */
    public function testShouldThrowExceptionIfHandledRequestContainsDoctype()
    {
        $server = new Server();
        $server->setOptions(array('location'=>'test://', 'uri'=>'http://framework.zend.com'));
        $server->setReturnResponse(true);

        $server->setClass('\ZendTest\Soap\TestAsset\ServerTestClass');

        $request =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<!DOCTYPE foo>' . "\n"
          . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" '
                             . 'xmlns:ns1="http://framework.zend.com" '
                             . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" '
                             . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                             . 'xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" '
                             . 'SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
          .     '<SOAP-ENV:Body>'
          .         '<ns1:testFunc2>'
          .             '<param0 xsi:type="xsd:string">World</param0>'
          .         '</ns1:testFunc2>'
          .     '</SOAP-ENV:Body>'
          . '</SOAP-ENV:Envelope>' . "\n";
        $response = $server->handle($request);

        $this->assertContains('Invalid XML', $response->getMessage());
    }

}
