<?php

define('SERVICE_FILE_NAME','MyService');	// SERVICE_FILE_NAME 类中含有方法的具体实现
define('SERVICE_CLASS_NAME','Class_Service');

include SERVICE_FILE_NAME.'.php';//引入service类

$server = new SoapServer(SERVICE_CLASS_NAME.'.wsdl', array('soap_version' => SOAP_1_2));
$server->setClass(SERVICE_CLASS_NAME); //注册 SERVICE_CLASS_NAME 类的所有方法 
$server->handle(); //处理请求

?>