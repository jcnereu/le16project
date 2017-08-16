<?php

/**
 * Verificar a segurança de se usar o GET para isso
 */

$idEspaco = $_GET['ide'];
$idUsuario = $_GET['idu'];

//Registrando a entrada do usuário
require_once '../models/space.class.php';

$spaceIn = new space();
if($spaceIn->registrarEntradaUsuario($idUsuario,$idEspaco)){
    $resposta = 'true';
} else {
    $resposta = 'false';
}
echo $resposta;
