<?php

define('SERVICE_FILE_NAME','MyService');	// SERVICE_FILE_NAME 类中含有方法的具体实现
define('SERVICE_CLASS_NAME','Class_Service');

include SERVICE_FILE_NAME.'.php';

if(!file_exists(SERVICE_CLASS_NAME.'.wsdl'))
{
	$disco = new SoapDiscovery(SERVICE_CLASS_NAME, 'MySoapDemo'); 
	//第一个参数是 SERVICE_CLASS_NAME.php中的类的名称
	//第二个参数是服务的名字（可以随便写）。
	
	$address_name=$_SERVER['PHP_SELF'];
	
	$disco->getWSDL($address_name);// 将生成 SERVICE_CLASS_NAME.wsdl文件
}else{
	$server = new SoapServer(SERVICE_CLASS_NAME.'.wsdl', array('soap_version' => SOAP_1_2));
	$server->setClass(SERVICE_CLASS_NAME); //注册 MyService 类的所有方法 
	$server->handle(); //处理请求
}

//下方是从网上复制过来的生成WSDL的库，但我给getWSDL()方法加了个参数


/**
 * Copyright (c) 2005, Braulio Jos?Solano Rojas
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 *     Redistributions of source code must retain the above copyright notice, this list of
 *     conditions and the following disclaimer.
 *     Redistributions in binary form must reproduce the above copyright notice, this list of
 *     conditions and the following disclaimer in the documentation and/or other materials
 *     provided with the distribution.
 *     Neither the name of the Solsoft de Costa Rica S.A. nor the names of its contributors may
 *     be used to endorse or promote products derived from this software without specific
 *     prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @version $Id$
 * @copyright 2005
 */

/**
 * SoapDiscovery Class that provides Web Service Definition Language (WSDL).
 *
 * @package SoapDiscovery
 * @author Braulio Jos?Solano Rojas
 * @copyright Copyright (c) 2005 Braulio Jos?Solano Rojas
 * @version $Id$
 * @access public
 * */
class SoapDiscovery 
{

    private $class_name = '';
    private $SERVICE_CLASS_NAME = '';

    /**
     * SoapDiscovery::__construct() SoapDiscovery class Constructor.
     *
     * @param string $class_name
     * @param string $SERVICE_CLASS_NAME
     * */
    public function __construct($class_name = '', $SERVICE_CLASS_NAME = '') {
        $this->class_name = $class_name;
        $this->SERVICE_CLASS_NAME = $SERVICE_CLASS_NAME;
    }

    /**
     * SoapDiscovery::getWSDL() Returns the WSDL of a class if the class is instantiable.
     *
	 * @param string $address_name
     * @return string
     * */
    public function getWSDL($address_name) {
        if (empty($this->SERVICE_CLASS_NAME)) {
            throw new \Exception('No service name.');
        }
        $headerWSDL = "<?xml version=\"1.0\" ?>\n";
        $headerWSDL.= "<definitions name=\"$this->SERVICE_CLASS_NAME\" targetNamespace=\"urn:$this->SERVICE_CLASS_NAME\" xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\" xmlns:tns=\"urn:$this->SERVICE_CLASS_NAME\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:SOAP-ENC=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns=\"http://schemas.xmlsoap.org/wsdl/\">\n";
        $headerWSDL.= "<types xmlns=\"http://schemas.xmlsoap.org/wsdl/\" />\n";

        if (empty($this->class_name)) {
            throw new \Exception('No class name.');
        }

        $class = new \ReflectionClass($this->class_name);

        if (!$class->isInstantiable()) {
            throw new \Exception('Class is not instantiable.');
        }

        $methods = $class->getMethods();

        $portTypeWSDL = '<portType name="' . $this->SERVICE_CLASS_NAME . 'Port">';
        $bindingWSDL = '<binding name="' . $this->SERVICE_CLASS_NAME . 'Binding" type="tns:' . $this->SERVICE_CLASS_NAME . "Port\">\n<soap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\" />\n";
        $serviceWSDL = '<service name="' . $this->SERVICE_CLASS_NAME . "\">\n<documentation />\n<port name=\"" . $this->SERVICE_CLASS_NAME . 'Port" binding="tns:' . $this->SERVICE_CLASS_NAME . "Binding\"><soap:address location=\"http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $address_name . "\" />\n</port>\n</service>\n";
        $messageWSDL = '';
        foreach ($methods as $method) {
            if ($method->isPublic() && !$method->isConstructor()) {
                $portTypeWSDL.= '<operation name="' . $method->getName() . "\">\n" . '<input message="tns:' . $method->getName() . "Request\" />\n<output message=\"tns:" . $method->getName() . "Response\" />\n</operation>\n";
                $bindingWSDL.= '<operation name="' . $method->getName() . "\">\n" . '<soap:operation soapAction="urn:' . $this->SERVICE_CLASS_NAME . '#' . $this->class_name . '#' . $method->getName() . "\" />\n<input><soap:body use=\"encoded\" namespace=\"urn:$this->SERVICE_CLASS_NAME\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</input>\n<output>\n<soap:body use=\"encoded\" namespace=\"urn:$this->SERVICE_CLASS_NAME\" encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" />\n</output>\n</operation>\n";
                $messageWSDL.= '<message name="' . $method->getName() . "Request\">\n";
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    $messageWSDL.= '<part name="' . $parameter->getName() . "\" type=\"xsd:string\" />\n";
                }
                $messageWSDL.= "</message>\n";
                $messageWSDL.= '<message name="' . $method->getName() . "Response\">\n";
                $messageWSDL.= '<part name="' . $method->getName() . "\" type=\"xsd:string\" />\n";
                $messageWSDL.= "</message>\n";
            }
        }
        $portTypeWSDL.= "</portType>\n";
        $bindingWSDL.= "</binding>\n";
        //return sprintf('%s%s%s%s%s%s', $headerWSDL, $portTypeWSDL, $bindingWSDL, $serviceWSDL, $messageWSDL, '</definitions>');
        //生成wsdl文件，将上面的return注释
        $fso = fopen(dirname(__FILE__) . '/' . $this->class_name . ".wsdl", "w");
        fwrite($fso, sprintf('%s%s%s%s%s%s', $headerWSDL, $portTypeWSDL, $bindingWSDL, $serviceWSDL, $messageWSDL, '</definitions>'));
    }

    /**
     * SoapDiscovery::getDiscovery() Returns discovery of WSDL.
     *
     * @return string
     * */
    public function getDiscovery() {
        return "<?xml version=\"1.0\" ?>\n<disco:discovery xmlns:disco=\"http://schemas.xmlsoap.org/disco/\" xmlns:scl=\"http://schemas.xmlsoap.org/disco/scl/\">\n<scl:contractRef ref=\"http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . "?wsdl\" />\n</disco:discovery>";
    }
}
