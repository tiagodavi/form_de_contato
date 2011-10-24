<?php
/**
 * @author Tiago Davi - tiagodavi.blogspot.com
 * @version 1.0
 * @package 18/10/2011
 */
class Annex{

	//Attributos do arquivo
	private $name, $tmp_name, $error, $size;
	
	//Diretório temporário onde o arquivo será salvo
	private $dir = 'tmp_anexos';
		
	public function __construct($params = array())
	{
		//Define os atributos no construtor
		if(isset($params['anexo']) && $params['anexo']['size'] > 0){
			foreach($params['anexo'] as $key => $value){
				if(property_exists($this, $key)){
					$this->$key = $value;
				}
			}
		}
		
		//Faz o upload
		$this->upload();
	}
	public function __get($name)
	{
		if(property_exists($this, $name)){
			return $this->$name;
		}
	}
	public function upload()
	{
		if($this->size > 0){
			//Cria o diretório se não existir
			if( ! is_dir($this->dir)){
				mkdir($this->dir, 0777);
			}
			return move_uploaded_file($this->tmp_name, $this->path_of_file());
		}
		else{		
			die('Falha no upload do anexo');
		}
	}
	public function remove_file()
	{
		//Remove o arquivo
		if(file_exists($this->path_of_file())){
			unlink($this->path_of_file());
		}else{
			die('Nenhum anexo foi removido');
		}
	}
	public function path_of_file()
	{
		//Caminho do arquivo
		return $this->dir.'/'.$this->name;
	}
}