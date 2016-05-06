<?php
/**
 * 
 * ����curl �Զ���curl , ����curl �����ȡhtml
 * @author fangjiefeng
 * @email fang.jief@163.com
 * @date 2015-02-10
 * @version 1.2
 */

class Scurl{

	public $isproxy = true;
	public $isAutoProxy = false;
	public $proxy='proxy.jgb:8081'; // 192.168.0.1:88@hexin:hx300033 ������@���и� �Զ���������д auto
    public $proxyfile = '/proxy.list';
	public $referer='';
    public $agent='Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.94 Safari/537.36';
    public $post; // post ���� array or str
	public $url='';
	public $head; //head ���� array or str
	public $isHead=false;
	public $isLocaltion = false;//�Ƿ���ת
	public $debug = false;
	public $cookie=false;//cookie str or cookie file
	public $cookiepath = '/tmp/';
	public $cookieotime = 3600;//cookie ��ʱʱ�� Ĭ��3600��
	public $maxtime = 60;//�������ʱ�� Ĭ��Ϊ60��
	public $proxyArr = [];
	
	
	
	public function request($url){
		$this->url=$url;
		return $this->ppopen($this->getcmd());
	}
	
	/**
	 * 
	 * ��cookie �ŵ�һ���ļ��У�������Ҫ�õ�cookie ����ֱ�ӵ���
	 */
	public function saveCookie(){
		if($this->cookie){
			$cookiefile = $this->cookiepath.$this->cookie;
			if(file_exists($cookiefile)){
				//���cookie û�г�ʱ��û�������µ�½
				if((time()-filectime($cookiefile)) > $this->cookieotime){
					unlink($cookiefile);
				}else{
					$this->post='';
					return true;
				}
			}
			if($this->debug){
				echo "cookie file: {$cookiefile}\n";
			}
			$cmd = $this->getcmd(2). " -c {$cookiefile} ";
			$res=$this->ppopen($cmd);
			@chmod($cookiefile,0777);
			$this->post='';
		}else{
			$this->post='';
			exit('������cookie');
		}
	}
	
	
	public function getStatus(){
		$this->isHead=true;
		if(!$this->url){
			exit("�������ú�url\n");
		}
		$cmd = $this->getcmd();
		$res = $this->ppopen($cmd);
		if(preg_match('/HTTP\/\d+\.\d+\s+(\d+)\s+\w+\n?/is',$res,$m)){
			echo $m[1];
			return $m[1];
		}
		//echo $res;
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $type
	 * 1 Ĭ������
	 * 2 save cookie
	 */
	private function getcmd($type=1){
		$this->cmd='';
		$this->cmd = "curl -s '{$this->url}' ";
		$this->__proxy();
		$this->__referer();
		$this->__agent();
		$this->__head();
		$this->__post();
		if($type !=2 ){
			$this->__cookie();
		}


		if(preg_match('/https/is',$this->url)){

			$this->cmd .= " -k ";
		}
		if($this->isHead){
			$this->cmd .= " -I ";
		}
		if($this->isLocaltion){
			$this->cmd .= " -L ";
		}
		if($this->maxtime){
			$this->cmd .=" -m {$this->maxtime} ";
		}
		
		if($this->debug){
		
			echo "$this->cmd\n";
		}
		return $this->cmd;
	}
	

	/**
	 * ���ô����������Զ� ���ֶ�����
	 * Enter description here ...
	 */
	private function __proxy(){
		if($this->isproxy && $this->proxy){
			if($this->isAutoProxy){
				//�Զ��������
				if(empty($this->proxyArr)) {
					$root = dirname(__FILE__);
					if(file_exists($root.$this->proxyfile)){
						$this->proxyArr = file($root.$this->proxyfile);
					}else{
						exit('�Զ����������ڵ�ǰĿ¼���ô����ļ� proxy.list ��ʽΪhost:port@user:pass �� host:port');
					}
				}
				$max=count($this->proxyArr)-1;
				$this->proxy = trim($this->proxyArr[rand(0,$max)]);
			}
			$proxyinfo=explode('@',$this->proxy);
			if(isset($proxyinfo[1])){
				$this->cmd .= " -x {$proxyinfo[0]} -U {$proxyinfo[1]} ";
			}else{
				$this->cmd .= " -x {$proxyinfo[0]} ";
			}
		}	
	}
	
	private function __referer(){
		if($this->referer){
			$this->cmd .= " -e '{$this->referer}' ";

		}	
	}
	
	private function __agent(){
		if($this->agent){
			$this->cmd .=" -A '{$this->agent}' ";
		}	
	}
	
	private function __head(){
		if($this->head){
			if(is_array($this->head)){
				foreach($this->head as $row){
					$this->cmd .= " -H '{$row}' ";
				}
			}else{
				$this->cmd .= " -H '{$this->head}' ";
			}
			
		}	
	}
	
	private function __cookie(){
		$cookiefile = $this->cookiepath.$this->cookie;
		if(file_exists($cookiefile)){
			$this->cmd .=  " -b '{$cookiefile}' ";
		}elseif(is_string($this->cookie)){
			$this->cmd .= " -b '{$this->cookie}' ";
		}
	}
	
	private function __post(){
		if($this->post){
			if(is_string($this->post)){
				$str = $this->post;
			}else{
				$str = http_build_query($this->post);
			}
			$this->cmd .= " -d '{$str}' ";
		}
		$this->post='';	
	}

	private function ppopen($cmd){
		$ft = popen($cmd,'r');
		$res='';
		while(!feof($ft)){
			$res.=fgets($ft,2048);
		}
		pclose($ft);
		return $res;

	}

	


}
