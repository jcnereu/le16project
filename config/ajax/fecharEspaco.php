<?php

$idEspaco = $_REQUEST['q'];

require_once '../models/space.class.php';// Saindo daqui mesmo
$espaco = new space();
//$espaco->registrarSaidaUsuario($idUsuario, $idEspaco);

// Apenas quando for o último usuário no espaço
// Para isso utilizar o método contarUsuarios
if($espaco->limparEspaco($idEspaco)){
    echo 'true';
} else {
    echo 'false';
}