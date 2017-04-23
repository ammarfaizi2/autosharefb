<?php
/**
* 
*
*
*
*/
$cf = array(
'email'=>'ammarfaizi93@gmail.com',
'user'=>'ammarfaizi93',
'pass'=>'858869123aaa',
'token'=>'',
'target'=>''
);



/**
*
*		Action
*
*/
require 'loader.php';
use System\Action_Handler;
$app = new Action_Handler($cf);
$app->run();


