<?php
// Pegando os dados enviados por formulário (via post) dentro do método JS onAthStateChanged()
$dadosFormulario = filter_input_array(INPUT_POST,FILTER_DEFAULT);
$nomeUsuarioFirebase = $dadosFormulario['userFirebaseName'];
$idUsuarioFirebase = $dadosFormulario['userFirebaseId'];

require_once '../loadConn.inc.php';// Sainda da pasta ajax
// Verificando se o usuário já existe (Se não existe faz o cadastro)
$buscaUsuario = new read();
$buscaUsuario->fazerBusca('SELECT * FROM users WHERE fb_uid = :bv', "bv={$idUsuarioFirebase}");
if($buscaUsuario->contaResultados()>0) {// Se o usuário já é cadastrado
    // Se não houver nenhuma sessão iniciada
    if(!session_id()):
        session_start();
    endif;
    // Pega os dados do usuário e aloca em uma varável global
    $_SESSION['dadosUsuario'] = array('id'=>$buscaUsuario->retornaResultado()[0]['id'],'fb_uid'=>$idUsuarioFirebase,'nome'=>$buscaUsuario->retornaResultado()[0]['name']);
    // Pode redirecionar
    $resposta = 'true';
} else { // Se o usuário não é cadastrado
    $novoUsuario = new create();
    $novoUsuario->fazerInsercao('users', array('fb_uid'=>$idUsuarioFirebase,'name'=>$nomeUsuarioFirebase));
    // Se a inserção foi bem sucedida
    if($novoUsuario->retornaResultado()){
        // Crinado a correspondencia do usuário na tabela userspaces
        $novoUsuarioEspaco = new create();
        $novoUsuarioEspaco->fazerInsercao('userspaces',array('id'=>$novoUsuario->retornaIDinserido(),'s1'=>0,'s2'=>0,'s3'=>0,'s4'=>0,'s5'=>0,'s6'=>0,'s7'=>0,'s8'=>0,'s9'=>0,'s10'=>0));
        if($novoUsuarioEspaco->retornaResultado()){
            // Se não houver nenhuma sessão iniciada
            if(!session_id()):
                session_start();
            endif;
            //Pega os dados do usuário e aloca em uma varável global
            $_SESSION['dadosUsuario'] = array('id'=>$novoUsuario->retornaIDinserido(),'fb_uid'=>$idUsuarioFirebase,'nome'=>$nomeUsuarioFirebase);
            // Pode redirecionar
            $resposta = 'true';
        } else {
            // Algo deu errado na inserção da userspaces, não deve redirecionar
            $resposta = 'false';
        }
    } else {
        // Algo deu errado na inserção da users, não deve redirecionar
        $resposta = 'false';
    }
}
// Resposta para o clientside
echo $resposta;

