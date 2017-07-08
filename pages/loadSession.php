<?php

/*
 - Código presente em todas as páginas após o Login;
 - Inicia a sessão com os dados do usuário na variável global $dadosUsuario;
 - Processa o logout (do FB ou conta independente)
 */

session_start();//Inicia a sessão possibilitando pegar a variável global $_SESSION[]
// Carregando a classe de login
require_once '../config/models/login.class.php';// Saindo da pages
// Criando um objeto da classe Login
$log = new login();
// Verificando se o usuário efetuou login (Em princípio, pode-se acessar qualquer página diretamente pela URL)
if($log->checarLogin()){//Se chegou na home através de login (Por segurança)
    $dadosUsuario = $_SESSION['dadosUsuario'];//Aloca todos os dados da sessão na variável $dadosUsuario
} else {
    unset($_SESSION['dadosUsuario']);//Encerra a sessão
    header('Location: ../index.php?exe=nologin');//Volta para página de Login/Cadastro
}
//Verifica a url e se estiver escrito 'logout' armazena o conteúdo (booleano) na variável logout
$logout = filter_input(INPUT_GET,'logout',FILTER_VALIDATE_BOOLEAN);
//Se o usuário clicar em sair
if($logout){
    unset($_SESSION['dadosUsuario']);//Encerra a sessão
    header('Location: ../index.php');//Redireciona para a página Login/Cadastro
}