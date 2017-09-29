<?php
    // Carregando o script para iniciar a sessão, fazer a verificação de login e processar o logout 
    require_once 'loadSession.php';
    /******************************************************************
     Código para validar acesso a um espaço e pegar os dados
     ******************************************************************/
    $idEspacoUrl = filter_input(INPUT_GET,'ids',FILTER_DEFAULT);
    if(isset($idEspacoUrl)){
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        if($espaco->validarAcessoEspaco($dadosUsuario['id'],$idEspacoUrl)){
            // Se o par usuário/espaço está registrado: Carrega as informações estáticas de criação do espaço
            $infoEspaco = $espaco->pegarInfoEspaco();
            $nomeEspaco = $infoEspaco['name'];
            $numeroUsuarios = $infoEspaco['nusers'];
            $criadorEspaco = $infoEspaco['creator_fbuid'];
            $dataCriacao = $infoEspaco['creation_date'];
        } else {
            header("Location: home.php?ss=ns&access=false");// Acrescentar uma mensagem específica informando que o usuário tentou uma operação não permitida (tentar acessar um espaço pela url)
        }
    }
    /******************************************************************
     Código para tratar as mensagens da área de conversa em um espaço (Deixado aqui em memória, feito pelo Firebase agora)
     ******************************************************************/
    /*
    if(isset($dadosFormulario['enviarMensagem'])){
        if(!empty($dadosFormulario['textoMensagem'])){
            include_once '../config/models/message.class.php';
            $mensagem = new message();
            $mensagem->publicarTexto($dadosFormulario['textoMensagem']);
            $mensagemFormatada = $mensagem->pegarMensagem();
        }
    }
    */
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
        <!-- Firebase codelab web CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
        <link type="text/css" rel="stylesheet" href="stylesheets/codelab.css">
        <!-- Material Design Lite -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script>
    </head>
    <body>
        <?php include_once 'userBar.php';?>
        <div class="coluna_central">
            <div class="coluna_central_c1">
                <div class="c1_lista_espacos" id="lista_espacos">
                    <?php
                        // Para guardar o id do último espaço na lista
                        $proximoEspaco = 0;
                        // Inicializando a string para guardar a lista de espaços
                        $listaEspacos = '';
                        // Buscando a linha do usuário na userspaces
                        include_once '../config/loadConn.inc.php';
                        $buscaLista = new read();
                        $buscaLista->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$dadosUsuario['id']}");
                        if($buscaLista->retornaResultado()>0){
                            // Criando uma div para conter o cabeçálio dos espaços listados
                            echo '<div class="cabecalio" id="tasc-tasc"></div>';
                            // Listando os espaços em que o usuário está registrado
                            foreach($buscaLista->retornaResultado()[0] as $coluna => $espaco){
                                // Guardando cada espaço registrado (inclusive o aberto) em uma string
                                if($coluna!='id' && $espaco!=0){
                                    $listaEspacos = $listaEspacos . "&{$espaco}";
                                }
                                // Criando o HTML/CSS para listar os espaços registrados com excessão do que está aberto
                                if($coluna!='id' && $espaco!=0 && $espaco!=$idEspacoUrl){ // O id do espaço aberto ($idEspacoUrl) é carregado por leitura da URL na etapa de validação acima
                                    // Pegando o nome do espaço na spaces
                                    $buscaLista->fazerBusca('SELECT name FROM spaces WHERE id = :bv',"bv={$espaco}");
                                    echo '<a href="home.php?ss=sp&ids=' .$espaco. '" class="c1_lista_espaco_container_individual">'
                                            .$buscaLista->retornaResultado()[0]['name']

                                            // DIV COM O NÚMERO DE USUÁRIOS
                                            .'<div class="numero_usuarios_espaco_listado">'
                                                .'<div class="icone"></div>'
                                                .'<div class="numero" id="numero_usuarios_' .$espaco. '"></div>'
                                            .'</div>'
                                            // DIV COM NÚMERO DE NOVAS MENSAGENS
                                            .'<div class="numero_msgs_espaco_listado" id="container_numero_msgs_' .$espaco. '">'
                                                .'<div class="icone"></div>'
                                                .'<div class="numero" id="numero_msg_' . $espaco. '"></div>'
                                            .'</div>'
                                     
                                        .'</a>'
                                            
                                        // ALTERAÇÃO FIREBASE: Criando listeners e exibindo os numéros de cada espaço
                                        // Sem os espaços no fim de cada linha o código não funciona
                                        .'<script> '
                                            // CABEÇALIO
                                            .'document.getElementById("tasc-tasc").innerHTML = "tasc tasc..."; '
                                            
                                            // NÚMERO DE USUÁRIOS
                                            .'var userListRef' .$espaco. ' = firebase.database().ref(\'spaces/space-' .$espaco. '\'); '
                                            .'userListRef' .$espaco. '.off(); '
                                            // Adição
                                            .'userListRef' .$espaco. '.on(\'child_added\', function(data) { '
                                                .'var numeroUsuariosAtual' .$espaco. ' = document.getElementById("numero_usuarios_' .$espaco. '").innerHTML; '
                                                .'document.getElementById("numero_usuarios_' .$espaco. '").innerHTML = numeroUsuariosAtual' .$espaco. ' - (-1); '
                                            .'}); '
                                            // Subtração
                                            .'userListRef' .$espaco. '.on(\'child_removed\', function(data) { '
                                                .'var numeroUsuariosAtual' .$espaco. ' = document.getElementById("numero_usuarios_' .$espaco. '").innerHTML; '
                                                .'document.getElementById("numero_usuarios_' .$espaco. '").innerHTML = numeroUsuariosAtual' .$espaco. ' - 1; '
                                            .'}); '
                                            
                                            // NÚMERO DE NOVAS MENSAGENS
                                            .'var msgListRef' .$espaco. ' = firebase.database().ref(\'counters/space-' .$espaco. '\'); '
                                            .'msgListRef' .$espaco. '.off(); '
                                            // Pegando número de mensagens uma vez ao carregar a lista
                                            .'msgListRef' .$espaco. '.once(\'value\').then(function(snapshot) { '
                                                .'var nMsgsInicial = snapshot.val().messages; '
                                                .'var contMsgs = 0; '
                                                .'var msgListUpdRef = firebase.database().ref(\'messages/space-' .$espaco. '\'); '
                                                // Somando +1 a cada mensagem adicionada
                                                .'msgListUpdRef.on(\'child_added\', function(data) { '
                                                    .'contMsgs = contMsgs + 1; '
                                                    .'if(contMsgs>nMsgsInicial) { '
                                                        .'document.getElementById("container_numero_msgs_' .$espaco. '").style.display = \'block\'; '
                                                        .'var nMsgsAtual = document.getElementById("numero_msg_' .$espaco. '").innerHTML; '
                                                        .'document.getElementById("numero_msg_' .$espaco. '").innerHTML = nMsgsAtual - (-1); '
                                                    .'} '
                                                .'}); '
                                            .'}); '
                                        .'</script>'
                                        ;
                                    // Pegando o ID do último espaço listado (primeiro aberto). Para pegar o útimo espaço aberto (primeiro na lista) pelo usuário deve-se implementar algum algoritmo simples aqui mesmo.
                                    $proximoEspaco = $espaco;
                                }
                            }
                        }
                    ?>
                </div>
                <!-- O campo abaixo é invisível. Criado apenas para servir o AJAX userSpaceCheckout, chamado ao fechar um espaço-->
                <input type="text" value="<?php echo $proximoEspaco; ?>" id="id_invisivel_proximo_espaco" style="display: none;">
                <!-- O campo abaixo é invisível. Criado apenas para servir o AJAX generalCheckout chamado ao sair do sistema-->
                <input type="text" value="<?php echo $listaEspacos; ?>" id="lista_invisivel_espacos" style="display: none;">
            </div>
            <div class="coluna_central_c2">
                <?php
                    $sessao = filter_input(INPUT_GET,'ss',FILTER_DEFAULT);// ss->sessão
                    if(empty($sessao)){
                        echo '<div class="c2_msg">Nenhum espaço aberto.</div>';
                    } else {
                        if($sessao=='ns'){
                            echo '<div class="c2_msg">Nenhum espaço aberto.</div>';
                        }elseif($sessao=='sp'){
                            include_once 'space.php';
                        }
                    }
                ?>
            </div>
        </div>
    </body>
</html>
