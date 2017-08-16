<?php

/**
 * Verificar a segurança de se usar o GET para isso
 */

$idEspaco = $_GET['ide'];
$idUsuario = $_GET['idu'];

require_once '../models/space.class.php';// Saindo daqui mesmo
$espaco = new space();
//$numeroUsuarios = $espaco->contarUsuarios($idEspaco);
//if($numeroUsuarios>1){
    if($espaco->registrarSaidaUsuario($idUsuario,$idEspaco)){
        $resposta = 'true';
    } else {
        $resposta = 'false';
    }
    
//} else {
  //  if($espaco->registrarSaidaUsuario($idUsuario,$idEspaco)){
    //    if($espaco->limparEspaco()){
      //      $resposta = 'true';
        //}
    //} else {
      //  $resposta = 'false';
    //}
//}
echo $resposta;
//$espaco->registrarSaidaUsuario($idUsuario, $idEspaco);

// Apenas quando for o último usuário no espaço
// Para isso utilizar o método contarUsuarios

