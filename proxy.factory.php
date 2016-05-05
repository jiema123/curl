<?php
require_once('curl.class.php');
$curl = new Scurl();

$curl->isproxy = true;
$curl->maxtime = 10;
$proxy = file('proxy.test.list');
$i = 0;
$proxyB = [];
while(true) {

	$proxyA = trim($proxy[$i]);
	$curl->proxy = $proxyA;
	$curl->url = 'http://1212.ip138.com/ic.asp';
	$html = $curl->getStatus();
	if ($html == 200) {
		$proxyB[] = $proxyA;
	}

	$i++;
	if($i > count($proxy)) {
		break;
	}
}

if (count($proxyB) > 0) {
	$textProxy = implode("\n", $proxyB);
	file_put_contents('proxy.list', $textProxy);
	echo "ok";
} else {
	echo "error";
}
