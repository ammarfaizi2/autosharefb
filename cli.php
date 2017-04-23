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
'token'=>'EAABwzLixnjYBAG0TtzhSNLwVED4puOzCZBlCsf6UQh480ZB8INTXZAfIpgeFZBgm5ZCqrUAMN0ZBjxthQZBkHrcLrH60zZAxvT14NiMMcJeKhx2jg9BtKzRSaGfjQgO8FljulrfZB5QWbGdBl1rdD3s34z89oUgNgL0JTvkPpqBANmQZDZD',
'target'=>'EsTehkuSegar'
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


