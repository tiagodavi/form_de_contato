<?php
/**
 * @author Tiago Davi - tiagodavi.blogspot.com
 * @version 1.0
 * @package 18/10/2011
 */
function __autoload($class)
{
	$dir = dirname(__FILE__) . '/' . 'lib' . '/';
	
	if(file_exists($dir.$class.'.php')){
		require_once($dir.$class.'.php');
	}
}
class Send{
	
	function __construct($_POST, $_FILES)
	{
		//O anexo dever ter o name = anexo
		if(count($_POST) > 0 && $_FILES['anexo']['size'] > 0){
			$this->send_annex($_POST, $_FILES);
		}elseif(count($_POST) > 0){
			$this->send_email($_POST);
		}else{
			print_r($_POST);
			print_r($_FILES);
			die('Seu post está vazio');
		}
	}
	function send_email($_POST)
	{
		$email = new Email($_POST);
		$email->send_email();
	}
	function send_annex($_POST, $_FILES)
	{
		$email = new Email($_POST);
		$annex = new Annex($_FILES);
				
		$email->send_annex($annex);
	}
}

$send = new Send($_POST, $_FILES);


