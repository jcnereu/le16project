<?php
    // Carregando o script para iniciar a sessão, fazer a verificação de login e processar o logout 
    require_once 'loadSession.php';
    // Pegando os dados do formulário se algum submit for clicado
    $dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    /******************************************************************
     Código para tratar a submissão do botão "Novo" na barra do usuário
     ******************************************************************/
    if(isset($dadosFormulario['submit_novo_espaco'])){
        $idUsuario = $dadosUsuario['id'];
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        // O método alocarEspaco já faz o registro de entrada do usuário (O primeiro usuário)
        if($espaco->alocarEspaco($dadosFormulario['nome_novo_espaco'],$idUsuario)){
            // Se tudo ocorreu bem, atualiza a URL para carregar o novo espaço
            header("Location: home.php?ss=sp&ids={$espaco->pegarIDespaco()}");
        }        
    }
    /******************************************************************
     Código para validar acesso a um espaço e pegar os dados
     ******************************************************************/
    $idEspacoUrl = filter_input(INPUT_GET,'ids',FILTER_DEFAULT);
    if(isset($idEspacoUrl)){
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        if($espaco->validarAcessoEspaco($dadosUsuario['id'],$idEspacoUrl)){
            // Se o par usuário/espaço está registrado: Carrega as infos do espaço (Apenas o nome por enquanto)
            $nomeEspaco = $espaco->pegarNomeEspaco();
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
                        // Listando os espaços em que o usuário se encontra
                        include_once '../config/loadConn.inc.php';
                        $buscaLista = new read();
                        $buscaLista->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$dadosUsuario['id']}");
                        if($buscaLista->contaResultados()>0){
                            foreach($buscaLista->retornaResultado()[0] as $coluna => $espaco){
                                // O id do espaço aberto ($idEspacoUrl) é carregado por leitura da URL na etapa de validação acima
                                if($coluna!='id' && $espaco!=0 && $espaco!=$idEspacoUrl){
                                    $buscaLista->fazerBusca('SELECT name FROM spaces WHERE id = :bv',"bv={$espaco}");
                                    echo '<a href="home.php?ss=sp&ids=' .$espaco. '" class="c1_lista_espaco_container_individual">'
                                            .$buscaLista->retornaResultado()[0]['name']
                                            .'</a>';
                                    // Pegando o último espaço listado (primeiro aberto). Para pegar o útimo espaço aberto (primeiro na lista) pelo usuário deve-se implementar algum algoritmo simples aqui mesmo.
                                    $proximoEspaco = $espaco;
                                }
                            }
                        }
                    ?>
                </div>
                <!-- O campo abaixo é invisível. Criado apenas para servir o JS chamado ao fechar um espaço-->
                <input type="text" value="<?php echo $proximoEspaco; ?>" id="id_invisivel_proximo_espaco" style="display: none;">
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
