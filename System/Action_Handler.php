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
	public function __construct($cf)
	{
		$fb = new Facebook($cf['email'],$cf['pass'],$cf['user'],$cf['token']);
		$a = $fb->login(array(52=>1));
	}
	private function chkck($file)
	{
		return file_exists($file)?(strpos(file_get_contents($file),'c_user')===false):true;
	}
	private function avoid_brute_login()
	{
		return file_exists()
	}
	public function run()
	{
		
	}
}