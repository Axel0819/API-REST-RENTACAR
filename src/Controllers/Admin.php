<?php
namespace App\Controllers;
    
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Admin extends baseCRUD{
    private $tabla = 'Administra';

    public function crear(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        $status= $this->crearBaseUsuario( $this->tabla, $body, 1 );
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function editar(Request $request, Response $response, $args){
        $body = json_decode($request->getBody());
        extract($args);
        $status= $this->editarBase( $this->tabla, $body,$codigo );
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function borrar(Request $request, Response $response, $args){
        extract( $args );
        $status= $this->borrarBase( $this->tabla, $codigo, "P" );
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus($status);
    }
}