<?php
    // Carregando o script para iniciar a sessão e tratar login/logout
    require_once 'loadSession.php';
    // Pegando os dados do formulário se algum submit for clicado
    $dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    /******************************************************************
     Código para tratar a submissão do botão "Novo" na barra do usuário
     ******************************************************************/
    if(isset($dadosFormulario['novoEspaco'])){
        $idUsuario = $dadosUsuario['id'];
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        // O método alocarEspaco já faz o registro de entrada do usuário
        if($espaco->alocarEspaco($dadosFormulario['nomeEspaco'],$idUsuario)){
            $idEspaco = $espaco->pegarIDespaco();
            /*
             * Verificar uma forma segura de redirecionar, sem informar o ID na url
             */
            header("Location: home.php?ss=sp&ids={$idEspaco}");
        }        
        
    }
    /******************************************************************
     Código para validar acesso a um espaço e pegar os dados
     ******************************************************************/
    $idEspacoUrl = filter_input(INPUT_GET,'ids',FILTER_DEFAULT);
    if(isset($idEspacoUrl)){
        $idUsuario = $dadosUsuario['id'];
        require_once '../config/models/space.class.php';// Saindo da home
        $espaco = new space();
        if($espaco->validarAcessoEspaco($idUsuario,$idEspacoUrl)){
            $nomeEspaco = $espaco->pegarNomeEspaco();
        } else {
            header("Location: home.php?ss=ns");// Acrescentar uma mensagem específica informando que o suário tentou uma operação não permitida (tentar acessar um espaço pela url)
        }
    }
    //##----$nomeEspaco ='Testes CSS do espaço aberto'; //##
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
        <?php include_once 'userBar.php';?>
        <div class="coluna_central">
            <div class="coluna_central_c1">
                <div class="c1_lista_espacos">
                    <?php
                        //##----Para testes CSS
                        $proximoEspaco = 0;
                        $idEspaco = filter_input(INPUT_GET,'ids',FILTER_DEFAULT);
                        include_once '../config/loadConn.inc.php';
                        $buscaLista = new read();
                        $buscaLista->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$dadosUsuario['id']}");
                        if($buscaLista->contaResultados()>0){
                            //##----$teste_css = array('s1'=>'teste de espaço','s2'=>'estilo do espaço','s3'=>'because the wind');
                            foreach($buscaLista->retornaResultado()[0] as $coluna => $espaco){
                            //##----foreach($teste_css as $coluna => $titulo){
                                if($coluna!='id' && $espaco!=0 && $espaco!=$idEspaco){
                                    $buscaLista->fazerBusca('SELECT name FROM spaces WHERE id = :bv',"bv={$espaco}");
                                    echo '<a href="home.php?ss=sp&ids=' .$espaco. '" class="c1_lista_espaco_container_individual">'
                                            //##----echo '<a href="" class="c1_lista_espaco_container_individual">' . $titulo . '</a>';
                                            .$buscaLista->retornaResultado()[0]['name']
                                            .'</a>';
                                    // Pegando o último espaço listado (primeiro aberto). Para pegar o útimo espaço aberto (primeiro na lista) pelo usuário deve-se implementar algum algoritmo simples aqui mesmo.
                                    $proximoEspaco = $espaco;
                                }
                            //##----}
                            }
                        }
                        
                        // PAROU AQUI: Pensar no layout quando apenas um espaço está berto.
                        
                    ?>
                </div>
                <!-- O campo abaixo é invisível. Criado apenas para serivir o JS chamado ao fechar um espaço-->
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
