<?php

//construccion de nuestra clase 
//conexion a la base de datos 

class Conexion{

    public static function conectar(){

        try{

            $cn=new PDO("mysql:host=localhost; dbname=gw_michiventuras", "root", "");
          
           return $cn; //retorna una conexion en todo momento

        }catch(PDOException $ex){
            die($ex->getMessage());
        }
    }

}

?>