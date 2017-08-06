<?php
    // Carregando o script para iniciar a sessão e tratar login/logout
    require_once 'loadSession.php';
    // Pegando os dados do formulário se algum submit for clicado
    $dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    /******************************************************************
     Código para tratar a submissão do botão "Novo" na barra do usuário
     ******************************************************************/
    if(isset($dadosFormulario['novoEspaco'])){
        //echo 'Recebido: '.$dadosFormulario['nomeEspaco'];
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        if($espaco->alocarEspaco($dadosFormulario['nomeEspaco'])){
            $idEspaco = $espaco->pegarIDespaco();
            /*
             * Verificar uma forma segura de redirecionar, sem informar o ID na url
             */
            header("Location: home.php?ss=sp&ids={$idEspaco}");
        }        
        
    }
    /******************************************************************
     Código para tratar as mensagens da área de conversa em um espaço
     ******************************************************************/
    if(isset($dadosFormulario['enviarMensagem'])){
        if(!empty($dadosFormulario['textoMensagem'])){
            include_once '../config/models/message.class.php';
            $mensagem = new message();
            $mensagem->publicarTexto($dadosFormulario['textoMensagem']);
            $mensagemFormatada = $mensagem->pegarMensagem();
            //var_dump($mensagemFormatada);
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>le16</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" rel="stylesheet" href="stylesheets/reset.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/home.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/userBar.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/space.css">
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
        </script>
        <?php include_once 'userBar.php';?>
        <div class="coluna_central">
            <div class="coluna_central_c1">
                <div>
                    c1
                </div>
            </div>
            <div class="coluna_central_c2">
                <?php
                    $sessao = filter_input(INPUT_GET,'ss',FILTER_DEFAULT);// ss->sessão
                    if(empty($sessao)){
                            echo '<div>Nenhum espaço aberto.</div>';
                    } else {
                        if($sessao=='ns'){
                            echo '<div>Vago</div>';
                        } elseif($sessao=='sp') {
                            include_once 'space.php';
                        }
                    }
                ?>
                <div>
                    c2
                </div>
            </div>
        </div>
    </body>
</html>
