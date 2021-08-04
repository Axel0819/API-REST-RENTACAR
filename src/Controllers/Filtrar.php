<?php
    namespace App\Controllers;
    
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    
class Filtrar extends AccesoBD{

    public function obtenerTodos(Request $request, Response $response, $args){
        extract($args);
        $datos= $this->todos( $tabla, $indice,$limite );
        $status= sizeof($datos) > 0 ? 200 : 204;

        $response->getBody()->write(json_encode( $datos ));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function porCodigo(Request $request, Response $response, $args){
        extract($args);
        $datos= $this->buscar( $tabla, $codigo );
        $status= sizeof($datos) > 0 ? 200 : 404;

        $response->getBody()->write(json_encode( $datos ));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function ejecutar(Request $request, Response $response, $args){
        extract($args);
        $valores = $request->getQueryParams();

        $retorno['cantidad']= $this->numRegs($tabla, $valores);
        $retorno['datos']= $this->filtrar($tabla, $valores,$indice,$limite);
        $status= ((int)($retorno['cantidad'])) > 0 ? 200 : 204;

        $response->getBody()->write(json_encode($retorno));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    //CORREGIR
    public function filtroDisponibles(Request $request, Response $response, $args){
        extract($args);
        $valores = $request->getQueryParams();

        $retorno['cantidad']= $this->numRegs($tabla, $valores);
        $retorno['datos']= $this->filtrar($tabla, $valores,$indice,$limite);
        die(var_dump($retorno['datos']));
        $status= ((int)($retorno['cantidad'])) > 0 ? 200 : 204;

        $response->getBody()->write(json_encode($retorno));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

}