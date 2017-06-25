<?php

// Constantes CSS
define('MSG_ACCEPT','msg_accept');
define('MSG_INFOR','msg_infor');
define('MSG_ALERT','msg_alert');
define('MSG_ERROR','msg_error');

// Menssagens do sistema, exibidas no clientside
// Não deve ser uma classe para que possa ser acessada em outras classes sem burocracia
function msgSistema($mensagem,$tipo,$Die=null){
    $classeCss=($tipo==E_USER_NOTICE?WS_INFOR:($tipo==E_USER_WARNING?WS_ALERT:($tipo==E_USER_ERROR?WS_ERROR:$tipo)));
    echo "<div class=\"caixa_msg_sistema {$classeCss}\"><span onclick=\"this.parentNode.style.display = 'none';\" class=\"botao_fechar_msg_sistema\"></span><p>{$mensagem}</p></div>";
    //Exemplo de como fechar uma div em: https://www.w3schools.com/jsref/prop_node_parentnode.asp
    if($Die):
        die;
    endif;
}