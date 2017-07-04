<?php
    session_start();//Inicia a sessão possibilitando pegar a variável global $_SESSION[]
    // Fazendo AutoLoad de classes e definições
    require_once '../config/models/login.class.php';// Sempre saindo da pasta Paginas
    // Criando um objeto da classe Login
    $log = new login();
    //MÉTODO PARA VERIFICAR O LOGIN
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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>LE16</title>
    <link type="text/css" rel="stylesheet" href="stylesheets/home.css" />
  </head>
  <body>
    <script>
        // Carregando o SDK do FB parte 1
        window.fbAsyncInit = function() {
            FB.init({
              appId      : '309116939544130',
              cookie     : true,
              xfbml      : true,
              version    : 'v2.8'
            });
            FB.AppEvents.logPageView();
            // Função para verificar o status do login ao carregar a página
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        };
        // Carregando o SDK do FB parte 2
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        // Função chamada para tratar a resposta da verificação do status
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                //document.getElementById("botaoSair").style.display = "block";
                olaUsuario();
            } else {
                // Ação provisória. Prever situação em que o usuário está logado com uma conta independente do FB.
                document.getElementById('alertaLogin').innerHTML = 'Usuário não logado pelo FB';
            }
        }
        // Função chamada caso o status seja 'connected'
        function olaUsuario() {
            //Função para pegar o nome do usuáio no FB. Deve ser utilizada na home.
            FB.api('/me', function(response) {
                document.getElementById('olaUsuario').innerHTML = 'Bem vindo, ' + response.name + '!';
            });
        }
        // Função chamada quando o usuário clica em logout
        function logout(){
            // Verfificar se o usuário está logado pelo fb
            // Se estiver sair com o fb
            // Se não, fazer logout normal
            FB.logout(function(response) {
               window.location.assign("../index.php");
            });
        }
    </script>
    Bem vindo à Home.
    <div id="olaUsuario"></div>
    <div id="alertaLogin"></div>
    <p>Olá <b><?php echo $dadosUsuario['nome']; ?></b></p>
    <br>
    <button onclick="logout();">Sair do fb</button>
    <a href="home.php?logout=true">Sair</a>
  </body>
</html>
