<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>LE16</title>
    <!-- ATENÇÃO:
        Antes de enviar para o GAE deve-se fazer a mudança:
            De: href="stylesheets/..." 
            Para: href="/stylesheets/..."
    -->
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
            FB.logout(function(response) {
               window.location.assign("../index.php");
            });
        }
        function voltar() {
            window.location.assign("../index.php");
        }
    </script>
    Bem vindo à Home.
    <div id="olaUsuario"></div>
    <div id="alertaLogin"></div>
    <br>
    <button onclick="logout();">Sair</button>
    <button onclick="voltar();">Voltar</button>
  </body>
</html>
