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
		return file_exists($file)?(strpos(file_get_contents($file),'c_user')==false):true;
	}
	private function avoid_brute_login()
	{
		return file_exists(data.'/avoid_brute_login.txt')?((int)file_get_contents(data.'/avoid_brute_login.txt')<5):true;
	}
	 function has_login()
	{
		return file_put_contents(data.'/avoid_brute_login.txt',(file_exists(data.'/avoid_brute_login.txt')?((int)file_get_contents(data.'/avoid_brute_login.txt')+1):1));
	}
	private function getnewpost()
	{
		$a = json_decode($this->curl(self::g.$this->tg.'/feed?limit=1&fields=id&access_token='.$this->fb->t),true);
		if(isset($a['data'][0]['id'])){
			return substr($a['data'][0]['id'],strpos($a['data'][0]['id'],'_')+1);
		} else {
			return false;
		}
	}
	private function gogo($url,$post=null,$op=null)
	{
			$a = $this->fb->go_to($url,$post,$op,'all');
			#var_dump($a);
			if(isset($a[1]['redirect_url']) and !empty($a[1]['redirect_url'])){
				$a = $this->gogo($a[1]['redirect_url'],$post,$op);
			}
			return $a;
	}
	private function share($pid)
	{
		/// get share link
	$src = $this->gogo(Facebook::url.$pid);
	 var_dump($src);
		$a = explode('href="/composer/mbasic/?c_src=share',$src[0],2);
		$a = explode('"',$a[1],2);
		// go to form
		$src = $this->gogo(Facebook::url."composer/mbasic/?c_src=share".html_entity_decode($a[0],ENT_QUOTES,'UTF-8'),null,array(CURLOPT_REFERER=>$src[1]['url']));
		$a = explode('<form',$src[0],2);
		$a = explode('</form',$a[1],2);
		$a = explode('type="hidden"',$a[0]);
		$p = array();
		$p['xc_message'] = "";
		for($i=1;$i<count($a);$i++){
			$b = explode('name="',$a[$i],2);
			$b = explode('"',$b[1],2);
			$c = explode('value="',$a[$i],2);
			$c = isset($c[1])?explode('"',$c[1],2):array('','');
			$p[$b[0]] = $c[0];
		}
		$p['view_post'] = "Bagikan";
		$ac = explode('action="',$src[0],2);
		$ac = explode('"',$ac[1],2);
		$ac = Facebook::url.html_entity_decode($ac[0],ENT_QUOTES,'UTF-8');
		return $this->gogo($ac,$p,array(CURLOPT_REFERER=>$src[1]['url']));
	}
	public function run()
	{
		#header('content-type:text/plain');
		#$this->share(1);
		if($this->chkck($this->fb->ck) and $this->avoid_brute_login()){
			$this->has_login();
			$a = $this->fb->login();
		}
		$data = file_exists(data.'/'.$this->tg.'.txt')?json_decode(file_get_contents(data.'/'.$this->tg.'.txt'),true):array();
		$data = $data==null?array():$data;
		$n = $this->getnewpost();
		if($n!==false and !in_array($n,$data)){
			$data[] = $n;
			$act = $this->share($n);
			#file_put_contents(data.'/'.$this->tg.'.txt',json_encode($data));
		} else {
			$act = "No Action";
		}
		print_r($data);
	}
}