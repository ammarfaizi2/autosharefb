<?php
namespace System;
use System\Crayner_Machine;
use System\Facebook2;
defined('data') or die('Data not defined !');
class Facebook extends Crayner_Machine
{
	const url = 'https://m.facebook.com/';
	private $e;
	private $p;
	public $ck;
	public $t;
	public function __construct($e,$p,$u=null,$t=null)
	{
		is_dir(data.'/cookies') or mkdir(data.'/cookies');
		$this->e = $e;
		$this->p = $p;
		$this->u = $u;
		$this->t = $t;
		if(isset($u)){
			$this->ck = data.'/cookies/'.$u.'.txt';
		} else {
			$e = explode('@',$e);
			$this->ck = data.'/cookies/'.$e[0].'.txt';
		}
	}
	public function go_to($url,$post=null,$op=null,$rt=null)
	{
		return parent::curl($url,$post,$this->ck,$op,$rt);
	}
	public function login()
	{
		$a = new Facebook2($this->e,$this->p,'',$this->u,$this->ck);
		return $a->login();
	}
	public function login2($op=null)
	{
		$op = array(CURLOPT_REFERER=>self::url,52=>true);
		$p = "email=".urlencode($this->e)."&pass=".urlencode($this->p);
		$a = $this->go_to(self::url);
		$s = explode('<form',$a,2);
		$a = explode('</form',$s[1],2);
		$a = explode('type="hidden"',$a[0]);
		for($i=1;$i<count($a);$i++){
			$b = explode('name="',$a[$i],2);
			$b = explode('"',$b[1],2);
			$c = explode('value="',$a[$i],2);
			$c = explode('"',$c[1],2);
			$p.= "&".$b[0]."=".urlencode($c[0]);
		}
		$s = $s[1];
		$a = explode('action="',$s,2);
		$a = explode('"',$a[1],2);
		$a = html_entity_decode($a[0],ENT_QUOTES,'UTF-8');
		$b = explode('type="submit"',$s,2);
		$b = explode('value="',$b[0],2);
		$b = explode('"',$b[1],2);
		$p.= "&login=".urlencode($b[0]);
		$a = $this->go_to($a,$p,$op);
		return $a;
	}
}