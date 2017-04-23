<?php
namespace System;
use System\Crayner_Machine;
use System\Facebook;
/**
*
*		@author Ammar Faizi <ammarfaizi2@gmail.com>
*		@license RedAngel PHP Concept
*
*		(Auto Share)
*
*/
class Action_Handler extends Crayner_Machine
{
	const g = 'https://graph.facebook.com/';
	private $tg;
	private $fb;
	public function __construct($cf)
	{
		$this->tg = $cf['target'];
		$this->fb = new Facebook($cf['email'],$cf['pass'],$cf['user'],$cf['token']);
	}
	private function chkck($file)
	{
		return file_exists($file)?(strpos(file_get_contents($file),'c_user')===false):true;
	}
	private function avoid_brute_login()
	{
		return file_exists(data.'/avoid_brute_login.txt')?((int)file_get_contents(data.'/avoid_brute_login.txt')<5):true;
	}
	private function has_login()
	{
		return file_put_contents(data.'/avoid_brute_login.txt',(file_exists(data.'/avoid_brute_login.txt')?((int)file_get_contents(data.'/avoid_brute_login.txt')+1):1));
	}
	private function getnewpost()
	{
		$a = json_decode($this->curl(self::g.$this->tg.'/feed?limit=1&fields=id&access_token='.$this->fb->t),true);
		if(isset($a['data'][0]['id'])){
			return substr($a['data'][0]['id'],strpos('_',$a['data'][0]['id'])+1);
		} else {
			return false;
		}
	}
	private function gogo($url,$post=null,$op=null)
	{
			$a = $this->fb->go_to($url,$post,$op,'all');
			if(isset($a[1]['redirect_url']) and !empty($a[1]['redirect_url'])){
				$a = $this->gogo($a[1]['redirect_url'],$post,$op);
			}
			return $a[0];
	}
	private function share($pid)
	{
	
	}
	public function run()
	{
		if($this->chkck($this->fb->ck) and $this->avoid_brute_login()){
			$this->fb->login();
			$this->has_login();
		}
		$data = file_exists(data.'/'.$this->tg.'.txt')?json_decode(file_get_contents(data.'/'.$this->tg.'.txt'),true):array();
		$n = $this->getnewpost();
		if($n!==false and !in_array($n,$data)){
			$data[] = $n;
			$act = $this->share($n);
			file_put_contents(data.'/'.$this->tg.'.txt',json_encode($data));
		} else {
			$act = "No Action";
		}
		print_r($act);
	}
}