<?php

class Usuario_E{


    //definimos cada una de las variables como propiedades de nuestra clase 

    private  $id_user;
    private  $nombre_usuario;  
    private  $fecha_registro;
    private  $correo;  
    private  $clave;  


	public function getId_user(){
		return $this->id_user;
	}

	public function setId_user($id_user){
		$this->id_user = $id_user;
	}

	public function getNombre_usuario(){
		return $this->nombre_usuario;
	}

	public function setNombre_usuario($nombre_usuario){
		$this->nombre_usuario = $nombre_usuario;
	}

	public function getFecha_registro(){
		return $this->fecha_registro;
	}

	public function setFecha_registro($fecha_registro){
		$this->fecha_registro = $fecha_registro;
	}

	public function getCorreo(){
		return $this->correo;
	}

	public function setCorreo($correo){
		$this->correo = $correo;
	}

	public function getClave(){
		return $this->clave;
	}

	public function setClave($clave){
		$this->clave = $clave;
	}

}

?>