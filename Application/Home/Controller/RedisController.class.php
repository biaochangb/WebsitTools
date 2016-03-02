<?php
namespace Home\Controller;
use Think\Controller;
class RedisController extends Controller {
	private $redis_server = null;
	private $redis_clear_key_prefix = "twitter:sg:event:python:debug18:";
	private $mapped = 1;
	/**
	 * initilize this controller.
	 */
	public function _initialize(){
		$this->redis_server = new \Think\Cache\Driver\Redis();
	}

	public function index(){
	}

	public function getAllEvents(){
		$keys =$this->redis_server->keys($this->redis_clear_key_prefix."*");
		$temp = $this->redis_server->mget($keys);
		$data =  array();
		foreach($temp as $event){
			$data[] = json_decode($event);
		}
		$this->ajaxReturn($data);
	}

	public function  get($eid = -1, $eid_old=0){
		if($eid == -1){
			$eid = $this->redis_server->get($this->redis_clear_key_prefix."nextId")-1;
		}else if($this->mapped === 1){
			$demo_episode = M('demo_episode');
			$condition = "eid={$eid}";
			if($eid_old > 0){
				$condition = $condition." and eid_old=".$eid_old;
			}

			$episodes = $demo_episode->where($condition)->order("eid_old desc")->limit(1)->select();
			foreach ($episodes as $episode) {
				$eid = $episode['eid_old'];
			}
		}
		//var_dump($eid);
		$data =$this->redis_server->get($this->redis_clear_key_prefix.$eid);
		//$this->ajaxReturn($data);
		return $data;		
	}

	/**
	 *  get the event summary.
	 * @param  integer $eid [event id]
	 * @return [type]       [event summary in JSON]
	 */
	public function getSummary($eid = -1, $eid_old = 0, $mapped = 1){
		$data =$this->get($eid, $eid_old);
		$this->mapped = $mapped;
		$this->ajaxReturn($data['summary']);
	}	

	/**
	 *  get the event-related geo-tweets.
	 * @param  integer $eid [event id]
	 * @return [type]       [tweets]
	 */
	public function getGeoTweets($eid = -1, $eid_old=0, $mapped = 1){
		//$eid = 1;
		$data =$this->get($eid, $eid_old);
		//$data =$this->redis_server->get($this->redis_clear_key_prefix.$eid);
		$this->ajaxReturn($data['summary']['relevant_geo_tweets']);
	}	
}
?>