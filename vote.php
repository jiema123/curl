<?php
$root = dirname(__FILE__);
require_once $root.'/curl.class.php';
$curl = new Scurl();
$curl->debug = true;
$curl->isproxy = true;
$curl->isAutoProxy = true;
$curl->agent = 'Mozilla/5.0 (Linux; Android 5.1.1; MX4 Pro Build/LMY48W) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036215 Safari/537.36 MicroMessenger/6.3.16.49_r03ae324.780 NetType/WIFI Language/zh_CN';
$opt = getopt('x:o:');
$curl->proxyNum = isset($opt['x']) ? $opt['x'] : null;

$i = 0;

$error = 0;
while(true) {
    if (isset($opt['o'])) {
        $curl->proxyNum += 1;
    }


    $isSleep = false;
    $curl->post['cc'] = 25;
    $curl->post['uid'] = md5(time().rand(1, 1000)).substr(md5(time().rand(1, 1000)), 0, 8);
    //$curl->url = 'http://vote.ecloud-zj.com/wx/voteSubmit';
    //$curl->url = 'http://vote.ecloud-zj.com/wx/Subget';
    $html = $curl->request('http://www.ecloud-zj.net/wx/Subget');
    if ($html > 0) {
        echo "成功".date("Y-m-d H:i:s \n");
        $curl->bestProxy[$curl->proxy] = $curl->proxy;
        $error = 0;
    } else {
        if (!is_null($curl->proxyNum)) {
            $error++;
        }
        if ($html == -9) {
            $html = ' 发现不能刷就1分钟后检测一次';
            $isSleep = true;
        }

        if (empty($html)) {
            $html = ' 返回为空对方服务器异常!';
        }

        echo "失败原因".date("Y-m-d H:i:s ").$html."\n";
        if(isset($curl->bestProxy[$curl->proxy])) {
            unset($curl->bestProxy[$curl->proxy]);
        }
        if ($isSleep) {
           sleep(60);
        }
    }



    if ($error > 100 && isset($opt['x'])) {
        break;
    }
    $i++;
}





