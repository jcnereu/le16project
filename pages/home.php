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
            $visibilidade = $infoEspaco['visible'];
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
        <link type="text/css" rel="stylesheet" href="stylesheets/c1.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/c2.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/c3.css">
        <link type="text/css" rel="stylesheet" href="stylesheets/list.css">
        <!-- Firebase codelab web CSS -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
        <!-- Material Design Lite -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <script defer src="https://code.getmdl.io/1.1.3/material.min.js"></script>
    </head>
    <body>
        <?php include_once 'userBar.php';?>
        <div class="coluna_central">
            <!--  ################################## COLUNA C1 ######################################-->
            <div class="c1" id="c1">
                <div class="dumper_top" id="dumper_top_c1"></div>
                <div class="cabecalio_resultado_busca" id="cabecalio_resultado_busca">Resultados:</div>
                <div class="resultado_busca" id="div_resultado_busca"></div>
                <div class="busca_sem_resultado" id="div_busca_sem_resultado"></div>
                <div class="lista_espacos_container" id="lista_espacos_container">
                    <?php
                        // Para guardar o id do último espaço na lista
                        $proximoEspaco = 0;
                        // Inicializando a string para guardar a lista de espaços
                        $listaEspacos = '';
                        // Buscando a linha do usuário na userspaces
                        include_once '../config/loadConn.inc.php'; // Saindo da home
                        $buscaLista = new read();
                        $buscaLista->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$dadosUsuario['id']}");
                        if($buscaLista->retornaResultado()>0){
                            // Criando uma div para conter o cabeçálio dos espaços listados
                            echo '<div class="cabecalio" id="cabecalio_lista_espacos"></div>';
                            // Listando os espaços em que o usuário está registrado
                            foreach($buscaLista->retornaResultado()[0] as $coluna => $espaco){
                                // Em cada campo de espaço não vazio da linha do usuário na tabela userspaces
                                if($coluna!='id' && $espaco!=0){
                                    // Pegando o nome do espaço na tabela spaces
                                    $buscaLista->fazerBusca('SELECT name FROM spaces WHERE id = :bv',"bv={$espaco}");
                                    // **************** PODE-SE BUSCAR E GUARDAR OUTRAS INFORMAÇÕES DO ESPAÇO AQUI
                                    // Guardando o ID e o nome de cada espaço registrado (inclusive o aberto) em uma string
                                    $listaEspacos = $listaEspacos . "&{$espaco}={$buscaLista->retornaResultado()[0]['name']}";
                           
                                    // Criando o HTML/CSS para listar os espaços registrados com excessão do que está aberto
                                    if($espaco!=$idEspacoUrl){ // O id do espaço aberto ($idEspacoUrl) é carregado por leitura da URL na etapa de validação acima
                                        
                                        echo '<a href="home.php?ss=sp&ids=' .$espaco. '" class="item_lista">'
                                                .'<div class="titulo">'.$buscaLista->retornaResultado()[0]['name'].'</div>'

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
                                                .'document.getElementById("cabecalio_lista_espacos").innerHTML = "Outras conversas"; '

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
                        }
                    ?>
                </div>
                <!-- O campo abaixo é invisível. Criado apenas para servir o AJAX userSpaceCheckout, chamado ao fechar um espaço-->
                <input type="text" value="<?php echo $proximoEspaco; ?>" id="id_invisivel_proximo_espaco" style="display: none;">
                <!-- O campo abaixo é invisível. Criado apenas para servir o AJAX generalCheckout chamado ao sair do sistema-->
                <input type="text" value="<?php echo $listaEspacos; ?>" id="lista_invisivel_espacos" style="display: none;">
            </div>
            <!--  ################################### COLUNA C2 #####################################-->
            <div class="c2" id="c2">
                <div class="dumper_top"></div>
                <?php
                    $sessao = filter_input(INPUT_GET,'ss',FILTER_DEFAULT);// ss->sessão
                    if(empty($sessao)){
                        echo '<div class="c2_msg_nenhum_espaco">Você pode buscar ou criar um tópico a qualquer momento.</div>';
                    } else {
                        if($sessao=='ns'){
                            echo '<div class="c2_msg_nenhum_espaco">Você pode buscar ou criar um tópico a qualquer momento.</div>';
                        }elseif($sessao=='sp'){
                            include_once 'space.php';
                        }elseif($sessao=='lt'){
                            include_once 'list.php';
                        }
                    }
                ?>
            </div>
            <!--  ################################### COLUNA C3 #####################################-->
            <div class="c3" id="c3">
                <?php include_once 'c3.php';?>
            </div>
        </div>
    </body>
</html>
