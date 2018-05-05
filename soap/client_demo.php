<?php
	ini_set('soap.wsdl_cache_enabled', "0"); //关闭wsdl缓存
	
	// 如果soap.php中 $address_name=$_SERVER['PHP_SELF'];
	$wsdl='http://localhost:88/soap.php?wsdl';
	
	// 如果soap.php中 $address_name='/server_in_another_file.php';
	// $wsdl='http://localhost:88/server_in_another_file.php?wsdl';
	
	$xx = new SoapClient($wsdl);
	echo $xx->HelloWorld();
	echo $xx->Add(28, 2);
	echo $xx->__soapCall('Add',array(28,2));//或这样调用