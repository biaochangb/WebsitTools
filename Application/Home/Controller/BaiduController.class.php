<?php
namespace Home\Controller;
use Think\Controller;
use \Think\Cache\Driver\Redis;
class BaiduController extends Controller {
	private $ZHIDAO_CHEST_URL = "http://zhidao.baidu.com/shop/submit/chest?type=";
	private $ZHIDAO_JingYanZhiShu_URL = "http://zhidao.baidu.com/submit/ajax/";

	public function index(){
		
	}

	public function httpPost($url, $data, $headers){ // 模拟提交数据函数      
		$curl = curl_init(); // 启动一个CURL会话      
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址                  
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查      
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器      
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求      
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包      
		curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息      
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环      
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);      
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回      
		$tmpInfo = curl_exec($curl); // 执行操作      
		if (curl_errno($curl)) {      
		 	echo 'Errno'.curl_error($curl);      
		}      
		curl_close($curl); // 关键CURL会话      
		return $tmpInfo; // 返回数据      
	} 

	public function zhidao(){
		// cm=100900&itemid=118&number=1
		// cm=100900&itemid=119&number=1
		// cm=100900&itemid=120&number=1
		$max_num = 100;
		$props = array('CopperChest', 'SilverChest','GoldChest', 'jyzs_small', 'jyzs_medium', 'jyzs_large');
		$itemIds = array('CopperChest'=>128, 'SilverChest'=>129,'GoldChest'=>130, 'jyzs_small'=>118, 'jyzs_medium'=>119, 'jyzs_large'=>120);
		foreach ($props as $type) {
			for($i=0;$i<$max_num;++$i){
				$url = "";
				$data  = "";
				if (strpos($type, "jyzs") !== false) {
					//经验之书
					$url = $this->ZHIDAO_JingYanZhiShu_URL;
					$data = "cm=100900&itemid=".$itemIds[$type]."&number=1";
				}else{
					$url = $this->ZHIDAO_CHEST_URL.$type;
					$data = 'itemId='.$itemIds[$type];	
				}
				$result = $this->getProps($url, $data);
				//var_dump($result);
				if ($result == null || $result['errno'] > 0 || $result['errorNo'] > 0) {
					var_dump($type." ".$i);
					//var_dump($url, $data);
					var_dump($result);
					break;
				}
			}
		}
	} 
	/**
	 *  调用
	 * 
	 * @param  [string] $url   领道具对应的ajax的url
	 * @param  [array] $data 道具对应的信息
	 * @return [json]	操作结果
	 */
	public function getProps($url, $data){
		$headers = array('Host: zhidao.baidu.com',
			'Connection: keep-alive',
			'Content-Length: 10',
			'Origin: http://zhidao.baidu.com',
			'X-Requested-With: XMLHttpRequest',
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36',
			'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
			'Referer: http://zhidao.baidu.com/static/ihome/item/html/wppop.html?v=2.1&itemId=118&num=18&view=6&show=0&lv=0',
			'Accept-Encoding: gzip, deflate',
			'Accept-Language: zh-CN,zh;q=0.8,en;q=0.6',
			'Cookie: BAIDUID=78B4FD99F88DD6FE5290AA525851C4B4:FG=1; BIDUPSID=78B4FD99F88DD6FE5290AA525851C4B4; PSTM=1440655609; BDUSS=hWMTR4R3BGOXNaQlkzdElJU0tWd1owSER-Z1l1RHhiU0ZYOXhWRmstUVJOZ1pXQVFBQUFBJCQAAAAAAAAAAAEAAAAxv4IKNmZvbmUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABGp3lURqd5Vej; bdshare_firstime=1440655698944; isFirstTag=1; ihomehq_tip_user=1; IK_USERVIEW=1; IK_CID_83=6; IK_78B4FD99F88DD6FE5290AA525851C4B4=43; IK_CID_74=37; CPLN=200; CPOFF=25; H_PS_PSSID=17084_1428_16479_16974_12772_12824_17012_12868_17105_16800_16905_17000_16935_17004_17072_15633_12372_13932_14550_16968_10632_16867_17051; Hm_lvt_6859ce5aaf00fb00387e6434e4fcc925=1440743277,1440838214,1440925048,1441003702; Hm_lpvt_6859ce5aaf00fb00387e6434e4fcc925=1441014642');

		$r = $this->httpPost($url, $data, $headers);
		//var_dump(mb_detect_encoding($r), $r);
		$result = json_decode($r, true);
		return $result;		
	}

	public function redis($eid = 1){
		$redis_server = new Redis();
		$data =$redis_server->get("twitter:sg:event:python:test3:".$eid);
		$this->ajaxReturn($data);
	}
}