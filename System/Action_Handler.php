<?php
namespace System;
use System\Crayner_Machine;
use System\Facebook;
header('content-type:text/plain');
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
		$this->ret = array('proc'=>array(),'share'=>'false');
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
		
		$a = json_decode($this->curl(self::g.$this->tg.'/feed?limit=10&fields=id,from&access_token='.$this->fb->t),true);
		foreach($a['data'] as $key => $qw){
			if($qw['from']['id']==$this->data['fpid']){
				$po = $key;
			}
		}
		if(isset($po) and isset($a['data'][$po]['id'])){
			return substr($a['data'][$po]['id'],strpos($a['data'][$po]['id'],'_')+1);
		} else {
			return false;
		}
	}
	private function login_act()
	{
		if($this->chkck($this->fb->ck) and $this->avoid_brute_login()){
			$this->has_login();
			$a = $this->fb->login();
			$this->ret['proc']['login'][] = "do login";
		} else {
			$this->ret['proc']['login'][] = "have login";
		}
	}
	public function run()
	{
		$this->getfpid();
	 $this->login_act();
		$this->fb->go_to(Facebook::url);
		$this->login_act();
		$n = $this->getnewpost();
		if($n!==false and !in_array($n,$this->data['post_list'])){
			$this->data['post_list'][] = $n;
			$act = $this->share($n);
			file_put_contents(data.'/'.$this->tg.'.txt',json_encode($this->data));
			print $act;
		} else {
			$act = "No Action";
		}
		print_r($this->ret);
		if($act=="No Action") print "Tidak ada postingan terbaru"; else print "Post baru !";
	}

	private function getfpid()
	{
		if(!file_exists(data.'/'.$this->tg.'.txt')){
		$a = $this->curl("https://graph.facebook.com/polybiusbank/?fields=id&access_token=".$this->fb->t);
		$a = json_decode($a,true);
		$this->data = array(
			'fpid'=>$a['id'],
			'post_list'=>array()		
		);
			$process = file_put_contents(data.'/'.$this->tg.'.txt',json_encode($this->data));
			$this->ret['proc']['getfpid'] = "save page id {$process} ".data.'/'.$this->tg.'.txt';
		} else {
			$this->ret['proc']['getfpid'] = "file_exists()";
			$this->data = json_decode(file_get_contents(data.'/'.$this->tg.'.txt'),true);
			if(!isset($this->data['fpid'])){
				throw new \Exception("Error JSON data !");
			}
		}
	}
	private function gogo($url,$post=null,$op=null)
	{
			$a = $this->fb->go_to($url,$post,$op,'all');
			if(isset($a[1]['redirect_url']) and !empty($a[1]['redirect_url'])){
				$a = $this->gogo($a[1]['redirect_url'],$post,$op);
			}
			return $a;
	}
	private function share($pid)
	{
		/// get share link
	$src = $this->gogo(Facebook::url.$pid);
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
		$this->ret['share'] = 'true';
		return $this->gogo($ac,$p,array(CURLOPT_REFERER=>$src[1]['url']));
	}
	
}