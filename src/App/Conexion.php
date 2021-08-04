<?php

use Psr\Container\ContainerInterface;

$container->set('bd',function(ContainerInterface $c){
    $config = $c->get('config_bd');

    $opcBD=[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];
    $dsn= "mysql:host=".$config->host.";dbname=".$config->bd.";charset=".$config->charset;

    try {
        $con= new PDO($dsn,$config->user,$config->password,$opcBD);
    } catch (PDOException $e) {
        print "¡Error! ". $e->getMessage(). "<br>";
        die();
    }
    return $con;
});