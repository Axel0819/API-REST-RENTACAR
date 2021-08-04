<?php
    namespace App\Controllers;
    
class BaseCRUD extends AccesoBD{

    public function crearBase($tabla, $body){
        $datos= $this->guardar( $tabla, $body );
        $status= $datos[0] > 0 ? 409 : 201;
    return $status;
    }

    public function editarBase($tabla, $body,$codigo){
        $datos= $this->guardar( $tabla, $body,$codigo );
        switch( $datos[0] ){
            case 0: $status = 404; break;
            case 1: $status = 200; break;
            case 2: $status = 409; break;
        }
    return $status;
    }

    public function borrarBase($tabla, $codigo, $st){
        $datos= $this->eliminarBD( $tabla, $codigo, $st );
        switch( $datos[0] ){
            case 0: $status = 404; break;
            case 1: $status = 200; break;
            case 2: $status = 406; break;
        }
    return $status;
    }
    public function crearBaseUsuario( $tabla, $body, $rol){
        $body->passw = password_hash($body->id, PASSWORD_BCRYPT, ['cost' => 11]);
        $datos=$this->crearTrans( $tabla, $body, $rol );
        return $datos[0] > 0 ? 409 : 201;
    }
}