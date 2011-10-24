<?php
/**
 * @author Tiago Davi - tiagodavi.blogspot.com
 * @version 1.0
 * @package 18/10/2011
 */
class Email{

	private	$from;
	private $to;
	private $subject;
	private $redirect_error;
	private	$redirect_success;
	private $name;
	private $post;
	
	public function __construct($params = array())
	{
		//Define os atributos no construtor
		if(count($params) > 0){
			foreach($params as $key => $value){
				//Define os atributos caso existam
				if(property_exists($this, $key)){
					$this->$key = $value;
				}else{
					//Atributos não existentes entram como post(mensagem)
					$this->post[$key] = $value;
				}
			}
		}
	}
	//Email comum
	public function send_email()
	{
		//Envia o email
		if(mail($this->to, $this->subject, $this->prepare_message(), $this->get_headers())){
			header("location: {$this->redirect_success}");
		}else{
			header("location: {$this->redirect_error}");
		}
	}
	//Email anexo
	public function send_annex($annex)
	{
		if(is_object($annex)){
				
			list($msg, $headers) = $this->get_headers_annex($annex);
			
			$annex->remove_file();
			
			if(mail($this->to, stripslashes($this->subject), $msg, $headers)){
				header("location: {$this->redirect_success}");
			}else{
				header("location: {$this->redirect_error}");
			}
		}
	}
	public function get_properties()
	{
		//Retorna os atributos do objeto
		return get_object_vars($this);
	}
	public function _print($var)
	{
		//Auxiliar para impressão
		echo '<pre>';
			print_r($var);
		echo '</pre>';
	}
	private function get_headers()
	{
		list($line, $headers) = $this->get_headers_so();
			
		$headers .= "MIME-Version: 1.0{$line}";
		$headers .= "Content-Type: text/html; charset=UTF-8{$line}";
			
		return $headers;
	}
	private function get_headers_annex($annex)
	{
		list($line, $headers) = $this->get_headers_so();
		
		$msg  = stripslashes($this->prepare_message());
		
		$file  		= fopen($annex->path_of_file(), "rb");
		$content 	= fread($file, filesize($annex->path_of_file()));
		$content 	= chunk_split(base64_encode($content));
		fclose($file);
			
		$headers.= "X-Mailer: Script para enviar arquivo atachado{$line}";
		$headers.= "MIME-version: 1.0{$line}";
		$headers.= "Content-type: multipart/mixed; ";
		$headers.= "boundary=\"Message-Boundary\"{$line}";
		$headers.= "Content-transfer-encoding: 7BIT{$line}";
		$headers.= "X-attachments: {$annex->name}";
			
		$top = "--Message-Boundary{$line}";
		$top.= "Content-type: text/html; charset=UTF-8{$line}";
		$top.= "Content-transfer-encoding: 7BIT{$line}";
		$top.= "Content-description: Mail message body{$line}{$line}";
			
		$msg = $top.$msg;
			
		$msg.= "{$line}--Message-Boundary{$line}";
		$msg.= "Content-type: multipart/alternative; name=\"{$annex->name}\"{$line}";
		$msg.= "Content-Transfer-Encoding: BASE64{$line}";
		$msg.= "Content-disposition: attachment; filename=\"{$annex->name}\"{$line}{$line}" ;
		$msg.= "{$content}{$line}";
		$msg.= "--Message-Boundary--{$line}";
		
		return array($msg, $headers);
	}
	private function get_headers_so()
	{
		//Headers de cada SO
		if(PHP_OS == "Linux"){
			$line 	  = "\n"; # For Linux
			$headers  = "From: {$this->name} <{$this->from}>{$line}";
		}
		elseif(PHP_OS == "WINNT"){
			$line 	  = "\r\n";# For Windows
			$headers  = "From: {$this->from}{$line}";
		}
		else{
			die("Este script nao esta preparado para funcionar com o sistema operacional de seu servidor");
		}
		
		return array($line, $headers);
	}
	private function prepare_message()
	{
		//Prepara a mensagem com os dados do post
		$message = "";
		foreach($this->post as $key => $value){
			$key = strtoupper($key);
			$message.= "<b>{$key}</b>: {$value} <br>";
		}
		return $this->html($message);
	}
	private function html($prepared_message)
	{
		//Html para o corpo do email
		if( ! empty($prepared_message)){
			
			$html = "<html>
						<head>
							<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
						</head>
						<body topmargin=0 style='margin:0p\\x; padding:0px; text-align:left; background-color:#eee;'>
							<table cellpadding='0' cellspacing='0' border='0' width='500' align='center' bgcolor='#ffffff'>
								<tr>
									<td valign='top'>
										<div align='left'>
											<a href='http://www.indexconsult.com.br/' target='_blank'>
												<img src='http://www.indexconsult.com.br/images/mail-topo.jpg' border='0'>
											</a>
										</div>
										<div align='left' style='font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333; line-height:16px; margin:20px'>
											{$prepared_message}
										</div>
									</td>
								</tr>
							</table>
						</body>
					</html>";
					
			return $html;
		}
	}
}