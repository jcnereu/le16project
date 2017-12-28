<!-- Subpágina carregada na home -->
<div class="dumper_top"></div>
<div class="cabecalio_c3"></div>
<!-- #################### DADOS DE CRIAÇÃO / LISTA DE USUÁRIOS / PERFIL / CONVITE ###################-->  
<div class="info_espaco_container" id="info_espaco_container">
    
    <!-- ############################### PERFIL USUÁRIO LISTADO #####################################-->
    <div class="perfil_usuario_listado_container" id="img_perfil_ampliada_container">
        <div class="cabecalio">
            <span class="voltar_link" id="fechar_img_ampliada" onclick="fecharImgPerfilAmpliada();">&#10094</span><!--id="fechar_img_ampliada"-->
            <div class="titulo">Perfil</div>
        </div>
        <div class="img_perfil_ampliada" id="img_perfil_ampliada"></div>
        <div class="textos_container">
            <div class="nome" id="nome_perfil">Nome Usuário</div>
            <div class="msg_status" id="msg_status_perfil">You won't see me.. Time after time you refuse to even listen</div>
        </div>
    </div>
    
    <!-- ################################# FORMULÁRIO DE CONVITE ################################### -->
    <div class="convite_container" id="convite_container">
        <div class="cabecalio">
            <div class="titulo">Convite</div>
        </div>
        <div class="campo">
            <div class="nome_campo">Convidar</div>
            <div class="nome_convidado" id="convite_nome_convidado"></div>
        </div>
        <div class="campo">
            <div class="nome_campo">Para</div>
            <select id="convite_lista_espacos"></select>
        </div>
        <div class="campo">
            <div class="nome_campo">Mensagem</div>
            <textarea id="convite_msg"></textarea>
        </div>
        <div class="campo">
            <div class="enviar_btn" onclick="enviarConvite();">Enviar</div>
            <div class="cancelar_btn" onclick="cancelarConvite();">Cancelar</div>
        </div>
    </div>

    <!-- ########################## DADOS DE CRIAÇÃO E LISTA DE USUÁRIOS ############################-->
    <div class="cabecalio">
        <span onclick="ocultarListaUsuarios();" class="voltar_link" id="lista_voltar_link">&times;</span>
        <div class="titulo">Info</div>
    </div>
    
    <!-- ################################### DADOS DE CRIAÇÃO #######################################-->
    <div class="detalhes">
        <div class="titulo"><?php echo $nomeEspaco; ?></div>
        <div class="faixa">
            <div class="usuario_criador">
                <div class="pic" id="user_creator_photo"></div>
                <div class="nome" id="user_creator_name"></div>
                <div class="data" id="data-criacao"></div>
            </div>
            <div class="outros_usuarios">
                <div class="numero" id="numero_usuarios"></div>
            </div>
        </div>
    </div>
    
    <!-- #################################### LISTA DE USUÁRIOS #####################################-->
    <div class="cabecalio_lista">
        Nesse momento:
    </div>
    <div id="container_lista_usuarios">
        <!-- Os usuários são inseridos aqui por JS -->
    </div>
    
</div>

<!-- ################################## ARÉA DE EDIÇÃO DE STATUS ####################################-->
<div class="status_edit_container" id="status_edit_container">
    <div class="cabecalio">
        <div class="titulo">Status</div>
    </div>
    <textarea id="user_msg_status_textarea"></textarea>
    <div class="rodape">
        <div class="btn_atualizar" onclick="atualizarMsgStatus();">Atualizar</div>
        <div class="btn_cancelar" onclick="cancelarEdicaoStatus();">Cancelar</div>
    </div>
</div>

<!-- ################################ LISTA DE CONVITES RECEBIDOS ###################################-->
<div class="lista_convites_container" id="lista_convites">
    <div class="cabecalio">
        <span onclick="ocultarListaConvites();" class="voltar_link" id="lista_voltar_link">&times;</span>
        <div class="titulo">Convites</div>
    </div>
    <!-- Os convites são inseridos aqui por JS -->
</div>

<!-- ########################################## OPÇÕES ############################################# -->
<div class="opcoes_container" id="opcoes_container">
    <div class="cabecalio">
        <span class="voltar_link" onclick="esconderOpcoes();">&times;</span>
        <div class="titulo">Opções</div>
    </div>
    <div class="item_opcoes">
        <div class="texto">Espaço visível?</div>
        <div class="radios">
            <label><input type="radio" name="visibilidade" id="visible_Y" onchange="mostrarConfirmaOpcao();"> Sim </label> &ensp;
            <label><input type="radio" name="visibilidade" id="visible_N" onchange="mostrarConfirmaOpcao();"> Não </label>
        </div>
    </div>
    <div class="confirma_opcao_container" id="confirma_opcao_container">
        <div class="confirmar_btn" onclick="atualizarOpcao();">Confirmar</div>
        <div class="cancelar_btn" onclick="cancelarOpcoes();">Cancelar</div>
    </div>
</div>