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
	public function run()
	{
		if($this->chkck($this->fb->ck) and $this->avoid_brute_login()){
			$this->fb->login();
			$this->has_login();
		}
	}
}