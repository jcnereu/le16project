<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>LE16</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="stylesheets/index.css" />
  </head>
  <body>
    <script>
        /*
         * LINK PARA ENTRAR COM O FACEBOOK
         */
        // Carregando o SDK do FB parte 1
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '309116939544130',
                cookie     : true,
                xfbml      : true,
                version    : 'v2.8'
            });
            FB.AppEvents.logPageView();
            // Função para verificar o status do login no FB ao carregar a página
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
                redirecionarUsuario();
            } else {
                // The person is not logged into your app or we are unable to tell.
                // Ação provisória
                document.getElementById('msgLogin').innerHTML = 'Faça login para participar.';
            }
        }
        // Função chamada caso o status seja 'connected'
        function redirecionarUsuario() {
            console.log('Carregando suas informações... ');
            window.location.assign("pages/home.php");
        }
        // Função chamada quando o usuário clicar no link (é um botão) "Entrar com o facebook"
        function chamarFbLogin() {
            FB.login(function(response) {
                statusChangeCallback(response);
            });
        }
        /*
         * CAIXA DE CRIAÇÃO DE CONTA (efeito modal)
         */
        // Se o usuário cliar no link (botão) criar conta
        function mostrarModal() {
            document.getElementById('modalBackground').style.display = "block";
        }
        // Se o usuário clicar no botão para fechar a criação de conta
        function fecharModal() {
            document.getElementById('modalBackground').style.display = "none";
        }
        // Se o usuário clicar fora da div de criação de conta
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalBackground')) {
                modalBackground.style.display = "none";
            }
        };
        // Se o usuário clicar em "Criar conta" e estiver tudo ok
        function msgEmailConfirmacao() {
            document.getElementById("modalBody").innerHTML = 'Olá "nome"! Enviamos um email de confirmação para "email informado". Agora basta acessar seu email, clicar no link "CONFIRMAR" e voltar aqui para entrar com a sua conta.';
        }
    </script>
    <div class="coluna_central">
        <div class="container_login_cadastro">
            <div class="subcontainer_login_cadastro">
                <form class="container_form_login">
                    <input type="email" name="emailEntrar" placeholder="email" required>
                    <input type="password" name="senhaEntrar" placeholder="senha" required>
                </form>
                <button style="color: #00CC7A;">Entrar</button>
                <button onclick="chamarFbLogin();">Entrar com o facebook</button>
                <button onclick="mostrarModal();">Criar conta</button>
            </div>
        </div>
        <div class="container_form_busca">
            <form method="post">
                <div class="caixa_texto_busca"><input type="text" name="content"></div>
            </form>
            <div id="msgLogin"></div>
            <br>
            <a href="pages/teste.php">Página de teste</a>
        </div>
        <p class="rodape">LE16 project. Day 10, working...</p>
    </div>
    <!--
    DIV COM EFEITO MODAL, contém o formulário para criar conta
    -->
    <div class="modal_background" id="modalBackground">
        <div class="modal_content" id="modalContent">
            <div class="modal_header">
                <span onclick="fecharModal();" class="modal_btn_close">&times;</span>
                <h2>Crie sua conta</h2>
            </div>
            <div class="modal_body" id="modalBody">
                <form>
                    <label>
                        Nome:<input type="text" required>
                    </label>
                    <label>
                        Email:<input type="email" required>
                    </label>
                    <label>
                        Senha:<input type="password" required>
                    </label>
                    <label>
                        Confirmar senha:<input type="password" required>
                    </label>
                    <input type="submit" value="Criar conta" onclick="msgEmailConfirmacao();">
                </form>
            </div>
        </div>
    </div>
  </body>
</html>
