<?php
ini_set('max_execution_time',false);
ini_set('display_errors',true);
ini_set('memory_limit','4G');
define('data',__DIR__.'/data');
is_dir(data) or mkdir(data);
spl_autoload_register(function($class){
	require __DIR__.'/'.str_replace('\\','/',$class).'.php';
});