<?php
    namespace App\Controllers;
    
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Firebase\JWT\JWT;
    
class Auth extends AccesoBD{
    private $tabla= 'Usuarios';
    
    private function autenticar($usr, $pass){
        $datos= $this->buscar($this->tabla, $usr);

        return (sizeof($datos) > 0 && password_verify($pass, $datos[0]->passw)) ? $datos : null;
    }

    private function generarTokens($usr, $rol){      
        $key = getenv('clave');
        $payload = [
            "iss" => $_SERVER['SERVER_NAME'],
            "iat" => time(),
            "exp" => time() + (300),
            "sub" => $usr,
            "rol" => $rol           
        ];
        $payloadRf = [
            "iss" => $_SERVER['SERVER_NAME'],
            "iat" => time(),
            "rol" => $rol           
        ];
        $tokenRf= JWT::encode($payloadRf, $key, 'HS256');
        $res = $this->modificarToken($usr, $tokenRf);

        return $resultado= [
            "token" => JWT::encode($payload, $key, 'HS256'),
            "tokenRefresh"=> $tokenRf
        ];
    }
    public function iniciarSesion(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());

        $datos= $this->autenticar($body->usr, $body->passw);

        if($datos){
            $resultado = $this->generarTokens($body->usr, $datos[0]->rol);
            $response->getBody()->write(json_encode($resultado));
            $status= 200;
        }else{
            $status= 401;
        }
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    public function cerrarSesion(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $datos= $this->modificarToken($body->usr);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function refrescarTokens(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $datos= $this->verificarRefresco($body->usr, $body->tkR);

        if(sizeof($datos) > 0){
            $resultado = $this->generarTokens($body->usr, $datos[0]->rol);
        }
        if( isset($resultado)){
            $status = 200;
            $response->getBody()->write(json_encode($resultado));
        }else{
            $status= 401;
        }
        //die(var_dump($resultado));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

}