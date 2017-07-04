<?php
    // VERIFICANDO A SESSÃO
    $bool = session_start();
    // ATALHO PARA A HOME (Deve ser aqui por causa do header)
    $atalho = filter_input(INPUT_GET,'atalho',FILTER_DEFAULT);
    if(isset($atalho)){
        if($atalho=='home'){
            if(!session_id()){
                session_start();
            }
            $_SESSION['dadosUsuario'] = array('id'=>1000,'email'=>'$teste@teste.com','senha'=>'12345','nome'=>'Teste');
        }
        header('Location: home.php', true, 301);
    }
    // Voltando a verificação da sessão
    var_dump($bool);
    if(empty($_SESSION['dadosUsuario'])):
        echo 'Sessão do usuário não encontrada.<br>';
    else:
        var_dump($_SESSION['dadosUsuario']);
    endif;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>LE16</title>
    <link type="text/css" rel="stylesheet" href="../stylesheets/index.css"/>
  </head>
  <?php
    // Pegando os dados do formulário se algum submit for clicado
    $dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
    // TESTE DE CHAMADA DE CLASSES E MÉTODOS
    $resultado = 0;
    if(isset($dadosFormulario['somar'])){
        // Carregando a classe de teste
        require_once '../config/teste/teste.class.php';
        $var = new teste();
        $resultado = $var->somar($dadosFormulario['num1'],$dadosFormulario['num2']);
    }
    // TESTES DE INTERAÇÃO COM O BANCO
    $buscaNome = '';
    $buscaCurso = '';
    $buscaPesquisa = '';
    if(isset($dadosFormulario['buscar'])){
        // Pegando o nome informado no formulário
        $nomeFormulario = $dadosFormulario['busca'];
        // Carregando as classes para interação com o banco de dados
        require_once '../config/loadConn.inc.php';
        // Fazendo a busca
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM usuarios WHERE nome = :nome',"nome={$nomeFormulario}");
        // Mostrando os resultados
        if($busca->contaResultados()>0){
            $buscaNome = $busca->retornaResultado()[0]['nome'];
            $buscaCurso = $busca->retornaResultado()[0]['curso'];
            $buscaPesquisa = $busca->retornaResultado()[0]['pesquisa'];
        }
    }
    $contaResultados = '';
    if(isset($dadosFormulario['contar'])){
        // Carregando as classes para interação com o banco de dados
        require_once '../config/loadConn.inc.php';
        // Fazendo a busca
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM usuarios');
        // Mostrando os resultados
        if($busca->contaResultados()>0){
            $contaResultados = $busca->contaResultados();
        }
    }
    $resultadoInsercao = '';
    if(isset($dadosFormulario['cadastrar'])){
        // Pegando os dados informados no formulário
        $nomeFormulario = $dadosFormulario['nome'];
        $cursoFormulario = $dadosFormulario['curso'];
        $pesquisaFormulario = $dadosFormulario['pesquisa'];
        // Carregando as classes para interação com o banco de dados
        require_once '../config/loadConn.inc.php';
        // Fazendo a inserção
        $insercao = new create();
        $insercao->fazerInsercao('usuarios',array('nome'=>$nomeFormulario,'curso'=>$cursoFormulario,'pesquisa'=>$pesquisaFormulario));
        if(!empty($insercao->retornaIDinserido())){
            $resultadoInsercao = $insercao->retornaIDinserido();
        }
    }
    $resultadoAtualizacao = false;
    if(isset($dadosFormulario['atualizar'])){
        if(!empty($dadosFormulario['idAtualizacao'])){
            // Pegando os dados informados no formulário
            $cursoFormulario = $dadosFormulario['curso'];
            $pesquisaFormulario = $dadosFormulario['pesquisa'];
            // Carregando as classes para interação com o banco de dados
            require_once '../config/loadConn.inc.php';
            // Fazendo a atualização
            $atualizacao = new update();
            $atualizacao->fazerAtualizacao('usuarios',array('curso'=>'dummy','pesquisa'=>'dummy'),"id={$dadosFormulario['idAtualizacao']}","curso={$cursoFormulario}&pesquisa={$pesquisaFormulario}");
            $resultadoAtualizacao = $atualizacao->retornaResultado();
        }
    }
    if($resultadoAtualizacao){
        $msgAtualizacao = 'Efetuada com sucesso.';
    } else {
        $msgAtualizacao = '';
    }
    $resultadoExclusao = false;
    if(isset($dadosFormulario['excluir'])){
        if(!empty($dadosFormulario['idExclusao'])){
            // Carregando as classes para interação com o banco de dados
            require_once '../config/loadConn.inc.php';
            // Fazendo a exclusão
            $exclusao = new delete();
            $exclusao->fazerExclusao('usuarios',"id = :id","id={$dadosFormulario['idExclusao']}");
            $resultadoExclusao = $exclusao->retornaResultado();
        }
    }
    if($resultadoExclusao){
        $msgExclusao = 'Efetuada com sucesso.';
    } else {
        $msgExclusao = '';
    }
    //TESTES COM AS MENSSAGENS DO SISTEMA
    if(isset($dadosFormulario['msg'])){
        require_once '../config/loadMsg.inc.php';
        if($dadosFormulario['msg']=='Infor'){
            $msgTeste = ['Mensagem de <b>Informação</b> padrão para teste.',MSG_INFOR];
        } elseif($dadosFormulario['msg']=='Accept'){
            $msgTeste = ['Mensagem de <b>Notificação de aceite</b> para teste.',MSG_ACCEPT];
        } elseif($dadosFormulario['msg']=='Alert'){
            $msgTeste = ['Mensagem de <b>Alerta</b> para teste.',MSG_ALERT];
        } elseif($dadosFormulario['msg']=='Error'){
            $msgTeste = ['Mensagem de <b>Erro</b> padrão para teste.',MSG_ERROR];
        }
    }
  ?>
  <body>
    <script>
        /* EXEMPLO DE REDIRECIONAMENTO COM JS*/
        function voltar() {
            window.location.assign("../index.php");
        }
    </script>
    <h1>Página de teste.</h1>
    <h2>Atalhos</h2>
    <ul>
        <a href="teste.php?atalho=home">Home (com sessão Teste, id 1000)</a>
    </ul>
    <h2>Chamada de classes e métdos</h2>
    <form method="post">
        Numero 1<input type="text" name="num1"><br>
        Numero 2<input type="text" name="num2"><br>
        <p>Soma</p><input type="text" value="<?php echo $resultado;?>">
        <input type="submit" name="somar" value="Somar">
    </form>
    <h2>Interação com o banco de dados</h2>
    <form method="post">
        Nome<input type="text" name="nome"><br>
        Curso<input type="text" name="curso"><br>
        Pesquisa<input type="text" name="pesquisa"><br>
        <input type="submit" name="cadastrar" value="Cadastrar"><br>
        <br>
        Busca por nome<input type="text" name="busca" style="width: 250px;"><input type="submit" name="buscar" value="Buscar"><br>
        Atualizar curso e pesquisa pelo ID<input type="text" name="idAtualizacao" style="width: 50px;"><input type="submit" name="Atualizar" value="Atualizar"><br>
        Excluir pelo ID<input type="text" name="idExclusao" style="width: 50px;"><input type="submit" name="excluir" value="Excluir"><br>
        <input type="submit" name="contar" value="Contar cadastros">
    </form>
    <p><b>Resultados:</b></p>
    <p>Nome: <?php echo $buscaNome;?></p>
    <p>Curso: <?php echo $buscaCurso;?></p>
    <p>Pesquisa: <?php echo $buscaPesquisa;?></p>
    <p>Cadastros: <?php echo $contaResultados;?></p>
    <p>Último ID inserido: <?php echo $resultadoInsercao;?></p>
    <p>Status atualização: <?php echo $msgAtualizacao;?></p>
    <p>Status exclusão: <?php echo $msgExclusao;?></p>
    <h2>Mensagens do sistema</h2>
    <?php
        if(isset($msgTeste)){
            msgSistema($msgTeste[0],$msgTeste[1]);
        }
    ?>
    <br>
    <form method="post">
        <input type="submit" name="msg" value="Infor">
        <input type="submit" name="msg" value="Accept">
        <input type="submit" name="msg" value="Alert">
        <input type="submit" name="msg" value="Error">
    </form>
    <a href="../index.php">index (link)</a>
    <button onclick="voltar();">index (botão com redir JS)</button>
  </body>
</html>
