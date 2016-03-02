<?php
namespace Home\Controller;
use Think\Controller;
class EventEvolutionController extends Controller {
	private $redis_server = null;
	private $redis_event_key_prefix = "changbiao:event:evolution:twitter:sg:";

	public function _initialize(){
		$this->redis_server = new \Think\Cache\Driver\Redis();
	}
	public function index(){
		var_dump($this->redis_server->lrange($this->redis_event_key_prefix."topics:filter", 0, -1));
	}

	public function topic($p=0){
		$active_key = $this->redis_event_key_prefix."topics:valid";
		$tid = $this->redis_server->lindex($active_key, $p);
		$topic = $this->redis_server->lindex($this->redis_event_key_prefix."topics:all", $tid);
		// var_dump($tid);
		// var_dump($topic);
		$topic = $this->formatString($topic);

		// var_dump($topic);
		$topic = json_decode($topic, true);
		//var_dump($topic);
		
		$events = array();
 		foreach($topic['events'] as $e){
 			$event = $this->redis_server->lindex($this->redis_event_key_prefix."events", $e);
 			//var_dump($event);
 			$event = json_decode($this->formatString($event), true);
 			//var_dump($event);
 			asort($event['keywords']);
 			$events[$e] = $event;
 		}
 		$data['p'] = $p;
 		$data['percentage'] = round(100*($p+1.0)/$this->redis_server->llen($active_key), 2);
		$data['topic'] = $topic;
		$data['events'] = $events;
		$this->assign($data);
		$this->display();
		// var_dump($data);
	}

	public function filterout($tid){
		$result = array();
		if($tid >= 0){
			$this->redis_server->rpush($this->redis_event_key_prefix."topics:invalid", $tid);
			$result['status'] = 1;
		}else{
			$result['status'] = 0;
		}
		$this->ajaxReturn($result);
	}

	/**
	 * add an edge ($u->$v) to the event evolution graph.
	 * @param int $tid topic id
	 * @param int $u event id
	 * @param int $v event id
	 */
	public function addEdge($tid, $u= -1, $v=-1){
		$result = array();
		if($u==-1 || $v==-1){
			$result['status'] = 0;
		}else{
			$this->redis_server->rpush($this->redis_event_key_prefix."topics:edges:".$tid, $u."_".$v);
			$result['status'] = 1;
		}
		$this->ajaxReturn($result);
	}

	public function formatString($s){
		$topic = str_replace("u'","'", $s);
		$topic = str_replace("u\"","\"", $topic);
		$topic = str_replace("set(","", $topic);
		$topic = str_replace(")","", $topic);
		$topic = preg_replace("('(\S+)+')","\"$1\"", $topic);
		$topic = preg_replace("('([0-9,-]+)\s([0-9,:]+)')","\"$1 $2\"", $topic);
		return $topic;
	}
}