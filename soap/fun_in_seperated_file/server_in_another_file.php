<?php

define('SERVICE_FILE_NAME','MyService');	// SERVICE_FILE_NAME ���к��з����ľ���ʵ��
define('SERVICE_CLASS_NAME','Class_Service');

include SERVICE_FILE_NAME.'.php';//����service��

$server = new SoapServer(SERVICE_CLASS_NAME.'.wsdl', array('soap_version' => SOAP_1_2));
$server->setClass(SERVICE_CLASS_NAME); //ע�� SERVICE_CLASS_NAME ������з��� 
$server->handle(); //��������

?>