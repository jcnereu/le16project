<!-- Subpágina carregada na home -->
<div class="dumper_top"></div><!-- YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY NOVO -->
<div class="cabecalio_c3"></div>
<!-- ########################## LISTA DE USUÁRIOS / PERFIL / CONVITE ################################-->  
<div class="info_espaco_container" id="lista_usuarios_container"><!-- id="info_espaco_container" YYYYYYYYYYYYYYYYYYYYYYYYYYYYYY NOVO lista_usuarios_container-->
    
    <!-- OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO F5 -->
    <!-- ####################### PERFIL USUÁRIO LISTADO#########################-->
    <div class="perfil_usuario_listado_container" id="img_perfil_ampliada_container">
        <div class="cabecalio">
            <span class="voltar_link" id="fechar_img_ampliada" onclick="fecharImgPerfilAmpliada();">&#10094</span><!--id="fechar_img_ampliada"-->
            <div class="titulo">Perfil</div>
        </div>
        <div class="img_perfil_ampliada" id="img_perfil_ampliada"></div>
        <div class="textos_container">
            <div class="nome">Nome Usuário</div>
            <div class="msg_status">You won't see me.. Time after time you refuse to even listen</div>
        </div>
    </div>
    
    <!-- OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO F5 -->
    <!-- ###################### FORMULÁRIO DE CONVITE ########################### -->
    <div class="convite_conatainer" id="convite_container">
        <div class="cabecalio"> <!-- OOOOO F5 -->
            <div class="titulo">Sem título</div><!-- OOOOO F5 -->
        </div><!-- OOOOO F5 -->
        <div class="campo">
            <div class="nome_campo">Convidar:</div>
            <div class="nome_convidado" id="convite_nome_convidado"></div>
        </div>
        <div class="campo">
            <div class="nome_campo">Para:</div>
            <select id="convite_lista_espacos"></select>
        </div>
        <div class="campo">
            <div class="nome_campo">Mensagem:</div>
            <textarea id="convite_msg"></textarea>
        </div>
        <div class="campo">
            <button onclick="enviarConvite();">ENVIAR</button>
            <button onclick="cancelarConvite();">CANCELAR</button>
        </div>
    </div>

    <!-- ####################### LISTA #########################-->
    <div class="cabecalio">
        <!-- OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO F5 -->
        <span onclick="ocultarListaUsuarios();" class="voltar_link" id="lista_voltar_link">&times;</span>
        <div class="titulo">Info</div>
    </div>
    <div id="container_lista_usuarios"></div>
    
</div>
<script>
 
</script>