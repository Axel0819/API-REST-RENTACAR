<?php
    namespace App\Controllers;
    
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    
class Usuario extends AccesoBD{

    private $tabla= 'Usuarios';
    
    public function obtenerTodos(Request $request, Response $response, $args){
        $limite = $args['limite'];
        $indice = $args['indice'];

        $datos= $this->todos( $this->tabla, $indice,$limite );
        $status= sizeof($datos) > 0 ? 200 : 204;

        $response->getBody()->write(json_encode( $datos ));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function porCodigo(Request $request, Response $response, $args){
        $codigo = $args['codigo'];
        //se busca por codigo
        $datos= $this->buscar( $this->tabla, $codigo );
        $status= sizeof($datos) > 0 ? 200 : 404;

        $response->getBody()->write(json_encode( $datos ));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function crear(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        
        $datos= $this->guardar( $this->tabla, $body );
        $status= $datos[0] > 0 ? 409 : 201;

        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function filtro(Request $request, Response $response, $args){
        $limite = $args['limite'];
        $indice = $args['indice'];
        $valores = explode('&',$args['valores']);

        unset($valores[sizeof($valores) - 1]);

        $datos= $this->filtrar($this->tabla, $valores,$indice,$limite);
        $status= sizeof($datos) > 0 ? 200 : 204;
        
        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function cambiarRol(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $codigo= $args['usr'];
        $datos= $this->cambioEnUsuario( "rolUsuarios", $body);
        $status= $datos[0] > 0 ? 200 : 404;

        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    private function autenticar($usr, $pass){
        $datos= $this->buscar($this->tabla, $usr);

        return (sizeof($datos) > 0 && password_verify($pass, $datos[0]->passw)) ? $datos : null;
    }

    public function cambiarPassw(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $usr= $args['usr'];

        $datos= $this->autenticar($usr, $body->passw);
        if($datos){
            $body->passwN = password_hash($body->passwN, PASSWORD_BCRYPT, ['cost' => 11]);
            //Se actualiza passN
            $datos = $this->cambioEnUsuario("passUsuarios", ['usr' => $usr, 'passw' => $body->passwN]);
            $status = 200;
        }else{
            $status = 403;
        }
        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    public function resetPassw(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $usr= $args['usr'];

        $datos= $this->buscar($this->tabla, $usr);
        if(sizeof($datos) > 0){
            $pass= password_hash($usr, PASSWORD_BCRYPT, ['cost' => 11]);
            //Se resetea el passw
            $datos = $this->cambioEnUsuario("passUsuarios", ['usr' => $usr, 'passw' => $pass]);
            $status = 200;
        }else{
            $status = 404;
        }
        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function borrar(Request $request, Response $response, $args){
        $codigo= $args['codigo'];
        //Se borrar curso
        $datos= $this->eliminarBD( $this->tabla, $codigo );
        $status= $datos[0] > 0 ? 200 : 404;

        $response->getBody()->write(json_encode($datos));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

}