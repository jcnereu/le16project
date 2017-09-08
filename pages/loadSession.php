<?php

/*
 - Código presente em todas as páginas após o Login;
 - Inicia a sessão com os dados do usuário na variável global $dadosUsuario;
 - Processa o logout (Depois de fazer signout do Firebase vem pra cá, destroi a sessão e redireciona para a index)
 */

// Se não houver nenhuma sessão iniciada
if(!session_id()):
    session_start(); // Inicia a sessão possibilitando pegar a variável global $_SESSION[]
endif;

// Verificando se o usuário efetuou login (Em princípio, pode-se acessar qualquer página diretamente pela URL)
if(!empty($_SESSION['dadosUsuario'])){//Se chegou na home através de login (Por segurança)
    $dadosUsuario = $_SESSION['dadosUsuario'];//Aloca todos os dados da sessão na variável $dadosUsuario
} else {
    unset($_SESSION['dadosUsuario']);//Encerra a sessão
    /*
     * Para recarregar a sessão podemos redirecionar para uma página intermediária com o mesmo mecanismo
     * de login na index, para criar uma nova sessão para o usuário e voltar para a home enquanto o
     * o mesmo está logado no Firebase. Nessa página intermediária, que será exibida por uma fração de segundos
     * podemos aproveitar e por uma mensagem bem humorada, pq não dá pra evitar esse passo mesmo.
     */
    header('Location: wayout.php');//Redireciona para uam página intermediária que faz signout do Firebase e redireciona para a index ao carregar
    //header('Location: ../index.php?exe=noSession');//Volta para a index com um flag para fazer singout do Firebase (Não dá certo)
}
//Verifica a url e se estiver escrito 'logout' armazena o conteúdo (booleano) na variável logout
$logout = filter_input(INPUT_GET,'logout',FILTER_VALIDATE_BOOLEAN);
//Se o usuário clicar em sair
if($logout){
    unset($_SESSION['dadosUsuario']);//Encerra a sessão
    header('Location: ../index.php');//Redireciona para a index
}