<?php

namespace App\Controllers;

use PDO;
use Psr\Container\ContainerInterface;

class AccesoBD{
    protected $container;
    
    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    private function generarParams($datos){
        $cad= "(";

        foreach($datos as $campo => $valor){
            $cad.= ":$campo,";
        }
        $cad= trim($cad, ',');
        $cad.= ");";

        return $cad;
    }
    public function todos($tabla, $pag, $limite){

        $indice= ($pag - 1) * $limite;

        $conexion = $this->container->get('bd');
        $sql= "call todos$tabla(:ind,:lim);";
        $consulta = $conexion->prepare($sql);

        $consulta->bindParam(':ind', $indice,PDO::PARAM_INT);
        $consulta->bindParam(':lim', $limite,PDO::PARAM_INT);
        $consulta->execute();

        $datos= [];

        if($consulta->rowCount() > 0){
            $i= 0;
            while($fila= $consulta->fetch(PDO::FETCH_ASSOC)){
                $i++;
                foreach($fila as $clave=> $valor){
                    $datos[$i][$clave]= $valor;  
                }
            }
        }
        $consulta= null;
        $conexion= null;

        return $datos;
    }

    public function buscar($tabla,$codigo){
        $conexion = $this->container->get('bd');
        $sql= "call buscar$tabla(:codigo);";//cambia para poder reutilizar
        $consulta = $conexion->prepare($sql);

        $consulta->bindParam(':codigo', $codigo,PDO::PARAM_STR);//cambia para poder reutilizar
        $consulta->execute();

        $datos= $consulta->fetchAll();

        $consulta= null;
        $conexion= null;

        return $datos;
    }

    public function guardar($tabla,$datos, $codigo=null){

        $params= $this->generarParams($datos);
        $conexion = $this->container->get('bd');
        $sql= $codigo != null ? "select editar$tabla$params" : "select nuevo$tabla$params";

        $consulta = $conexion->prepare($sql);

        $d=[];
        foreach($datos as $campo => $valor){
            $d[$campo]= filter_var($valor, FILTER_SANITIZE_STRING);
        }
        
        $consulta->execute($d); 

        $datos= $consulta->fetch(PDO::FETCH_NUM);

        $consulta= null;
        $conexion= null;

        return $datos;
    }

    public function eliminarBD($tabla,$codigo, $st){
        $sent = $st == "F" ? "select": "call";
        $conexion = $this->container->get('bd');
        $sql= "$sent eliminar$tabla(:codigo);";

        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':codigo', $codigo,PDO::PARAM_STR);
        $consulta->execute();

        $datos= $consulta->fetch(PDO::FETCH_NUM);

        $consulta= null;
        $conexion= null;

        return $datos;
    }
    public function filtrar($tabla,$valores, $pag, $lim){
        $ind= ($pag - 1) * $lim;
        $cad= "";
        foreach($valores as $valor){
            $cad .= "%$valor%&";
        }
        $conexion = $this->container->get('bd');
        $consulta= $conexion->prepare("call filtrar$tabla(:cadena,$ind,$lim);");
        $consulta->bindParam(':cadena', $cad,PDO::PARAM_STR);
        
        $consulta->execute();
        $datos= $consulta->fetchAll();
        $conexion= null;
        $consulta= null;

        return $datos;
    }
    public function numRegs($tabla,$valores){
        $cad= "";
        foreach($valores as $valor){
            $cad .= "%$valor%&";
        }
        $conexion = $this->container->get('bd');
        $consulta= $conexion->prepare("select numRegs$tabla(:cadena);");
        $consulta->bindParam(':cadena', $cad,PDO::PARAM_STR);
        
        $consulta->execute();
        $datos= $consulta->fetch(PDO::FETCH_NUM);
        $conexion= null;
        $consulta= null;

        return $datos;
    }
    //Vehiculo
    
    //usuarios
    public function cambioEnUsuario($proc,$datos){
        $params= $this->generarParams($datos);
        $conexion = $this->container->get('bd');
        $sql= "select $proc$params";

        $consulta = $conexion->prepare($sql);

        $d=[];
        foreach($datos as $campo => $valor){
            $d[$campo]= filter_var($valor, FILTER_SANITIZE_STRING);
        }
        
        $consulta->execute($d); 

        $datos= $consulta->fetch(PDO::FETCH_NUM);

        $consulta= null;
        $conexion= null;

        return $datos;
    }

    public function crearTrans($tabla,$datos, $rol){
        $pass= $datos->passw;
        unset($datos->passw);
        $params= $this->generarParams($datos);
        $conexion = $this->container->get('bd');
        $conexion->beginTransaction();

        try {
    //creando el registro
        $sql= "select nuevo$tabla$params";
        $consulta = $conexion->prepare($sql);
        $d=[];
        foreach($datos as $campo => $valor){
            $d[$campo]= filter_var($valor, FILTER_SANITIZE_STRING);
        }
        $consulta->execute($d); 
    //creando usuario
        $sql= "select nuevoUsuarios(:usr,:rol,:passw)";
        $consulta = $conexion->prepare($sql);
        $consulta->execute(array(
            'usr' => $datos->id,
            'rol' => $rol,
            'passw' => $pass
        ));
        $conexion->commit();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            $conexion->rollback();
        }

        $datos= $consulta->fetch(PDO::FETCH_NUM);
        $consulta= null;
        $conexion= null;

        return $datos;
    }
    public function modificarToken($usr, $tokenR=""){
        $conexion = $this->container->get('bd');
        $sql ="select modificarToken(:usr,:tk);";
        $query = $conexion->prepare($sql);
        $query->execute(Array('usr'=>$usr, 'tk'=>$tokenR));
        $datos = $query->fetch(PDO::FETCH_NUM);
        $query = null;
        $conexion = null;
        return $datos;
    }
    public function verificarRefresco($usr, $tokenR){
        $conexion = $this->container->get('bd');
        $sql ="call verificarTokenR(:usr,:tk);";
        $query = $conexion->prepare($sql);
        $query->execute(Array('usr'=>$usr, 'tk'=>$tokenR));
        $datos = $query->fetchAll();
        $query = null;
        $conexion = null;
        return $datos;
    }
}