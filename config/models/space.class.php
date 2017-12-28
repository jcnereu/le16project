<?php

/**
 * Description of space
 *
 * @author JoaoCarlosNereu
 */
class space {
    
    private $name;
    private $id;
    private $info; // Array com as informações ESTÁTICAS exibidas no espaço: Nome, criador e data de criação (Por enquanto)
    private $userID;
    private $userAvailableColumn;
    private $emptySpace = false; // Flag para informar o clientSide se o espaço está vazio (true) ou ocupado (false)

    // Método chamado quando o usuárip clica em "Novo" para abrir um espaço na home
    // Ao chamar o método registrarEntradaUsuario aqui dentro criou-se um bug de 3 dias
    // Ficou conhecido como bug do id 1
    public function alocarEspaco($name,$idUsuario) {
        $this->name = (String) strip_tags(trim($name));
        $this->userID = $idUsuario;
        require_once '../loadConn.inc.php';// Saindo do createNewSpace na pasta ajax
        if($this->procurarVagaUsuario()=='ok'){ // DDDDDDDDDDDDDDD F5
            if($this->reciclarOuCriarVaga()) {// Gera o id do espaço, atua na tabela spaces
                   $atualizacao = new update();
                   $atualizacao->fazerAtualizacao('userspaces',array("{$this->userAvailableColumn}"=>'dummy'),"id={$this->userID}","{$this->userAvailableColumn}"."={$this->id}");
                   return true; 
            } else { // Se deu algo errado na aloação de um espaço
                return false;
            }
        } else {
            return false; //Msg: Não tem mais vaga. Sair de um espaço para entrar em outro.
        }
    }
    
    // Método utilizado ao entrar no espaço por uma busca
    public function registrarEntradaUsuario($idUsuario,$idEspaco) {
        $this->userID = $idUsuario;
        $this->id = $idEspaco;
        require_once '../loadConn.inc.php';// Saindo do userSpaceCheckin na pasta ajax
        if($this->procurarVagaUsuario()=='ok'){// DDDDDDDDDDDDDDDDDDDDD F5
            $atualizacao = new update();
            $atualizacao->fazerAtualizacao('userspaces',array("{$this->userAvailableColumn}"=>'dummy'),"id={$this->userID}","{$this->userAvailableColumn}"."={$this->id}");
            if($this->atualizarNumeroUsuarios('mais')){
               return 'ok';//true; 
            } 
        } elseif ($this->procurarVagaUsuario()=='limite') { // DDDDDDDDDDDDDDDDDDDD F5
            // Msg: Usuário já está no limite de espaços (10). Deve sair de um para entrar em outro
            return 'limite';//false;
        } else { // DDDDDDDDDDDDDDDDDDDDD NOVO
            return 'erro'; 
        }
    }
    
    public function registrarSaidaUsuario($idUsuario,$idEspaco) {
        $this->userID = $idUsuario;
        $this->id = $idEspaco;
        require_once '../loadConn.inc.php';// Saindo do userSpaceCheckout na pasta ajax
        $busca = new read();
        // Primeiro encontra em qual coluna da userspaces (na linha do usuário) o espaço informado está registrado 
        $busca->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$this->userID}");
        foreach ($busca->retornaResultado()[0] as $coluna => $space){
            if($coluna!='id' && $space==$this->id){ // Para não atualizar o id huaahauah
                $colunaEspaco = $coluna;
            }
        }
        // Depois atualiza a linha do usuário na coluna encontrada no passo anterior
        $atualizacao = new update();
        $atualizacao->fazerAtualizacao('userspaces',array("{$colunaEspaco}"=>'dummy'),"id={$this->userID}","{$colunaEspaco}"."=0");
        // Por último atualiza a spaces subtraindo 1 do campo nusers na linha do espaço 
        if($atualizacao->retornaResultado()){
            if($this->atualizarNumeroUsuarios('menos')){
                return true;
            }
        } else {
            return false;
        }   
    }
    
    public function validarAcessoEspaco($idUsuario,$idEspaco) {
        $this->userID = $idUsuario;
        $this->id = $idEspaco;
        require_once '../config/loadConn.inc.php';// Saindo da home
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM spaces WHERE id = :bv',"bv={$this->id}");
        if($busca->contaResultados()>0){
            if($this->verificarRegistroUsuario()){
                 // Retornando os dados (nome, criador e data de criação) para uso no clientSide
                $this->info = $busca->retornaResultado()[0];
                return true;
            } else {
                return false; // O usuário não foi eregistrado e portanto não pode ter acesso a esse espaço
            }
        } else {
            return false; // O espaço não existe ou ocorreu algum problema na leitura
        }
    }
    
    // Futuramente pode ser necessário atualizar outra opção além da visibilidade
    public function atualizarVisibilidade($idEspaco, $visibilidade) {
        require_once '../config/loadConn.inc.php';// Saindo da home
        $atualizacao = new update();
        $atualizacao->fazerAtualizacao('spaces', array('visible'=>'dummy'), "id={$idEspaco}", "visible={$visibilidade}");
        return $atualizacao->retornaResultado();
    }
    
    public function pegarIDespaco() {
        return $this->id;
    }

    public function pegarInfoEspaco() {
        return $this->info;
    }
    
    public function pegarEmptySpace() {
        return $this->emptySpace;
    }
    
    // Métodos privados
    
    private function reciclarOuCriarVaga() {
        if($this->procurarVaga()){
            // Pegando o ID fairebase do usuário
            $buscaFbId = new read();
            $buscaFbId->fazerBusca('SELECT fb_uid,name FROM users WHERE id = :bv',"bv={$this->userID}");
            // Unix time (ou UTC): tempo absoluto em segundos desde 01/01/1970 ver o wikipedia sobre
            $utime = time();
            // Fazendo a atualização da vaga (linha na spaces) com os dados recebidos
            $atualizacao = new update();
            $atualizacao->fazerAtualizacao('spaces',array('name'=>'dummy','nusers'=>'dummy','creator_fbuid'=>'dummy','creator_name'=>'dummy','creation_date'=>'','status'=>'dummy'),"id={$this->id}","name={$this->name}&nusers=1&creator_fbuid={$buscaFbId->retornaResultado()[0]['fb_uid']}&creator_name={$buscaFbId->retornaResultado()[0]['name']}&creation_date={$utime}&status=on");
            if($atualizacao->retornaResultado()){
                return true;
            } else {
                return false; // Se algo deu errado no caminho e a atualização não foi realizada
            }
        } else { // Não achou nenhuma vaga
            if($this->criarVaga()){
                return true;
            } else {
                return false; // Se algo deu errado no caminho e a inserção não foi realizada
            }
            
        }
    }
    
    private function procurarVaga() {
        $busca = new read();
        $busca->fazerBusca('SELECT id FROM spaces WHERE status = :bv LIMIT 1',"bv=off");
        if(!empty($busca->retornaResultado()[0]['id'])){
            $this->id = $busca->retornaResultado()[0]['id'];
            return true;
        } else {
            return false;
        }
    }
    
    private function criarVaga() {
        // Pegando o ID fairebase do usuário
        $buscaFbId = new read();
        $buscaFbId->fazerBusca('SELECT fb_uid,name FROM users WHERE id = :bv',"bv={$this->userID}");
        // Unix time (ou UTC): tempo absoluto em segundos desde 01/01/1970 ver o wikipedia sobre
        $utime = time();
        // Fazendo a inserção (nova linha na spaces) com os dados recebidos
        $insercao = new create();
        $insercao->fazerInsercao('spaces',array('name'=>$this->name,'nusers'=>1,'creator_fbuid'=>$buscaFbId->retornaResultado()[0]['fb_uid'],'creator_name'=>$buscaFbId->retornaResultado()[0]['name'],'creation_date'=>$utime,'status'=>'on'));
        if($insercao->retornaResultado()){
            $this->id = $insercao->retornaIDinserido(); // Pegando o novo ID 
            //echo 'Vaga criada de novo.';
            return true;
        } else {
            return false; // Se algo deu errado no caminho e a inserção não foi bem sucedida
        }
    }
    
    private function procurarVagaUsuario() {
        
        // Este método também verifica se o usuário já está no espaço clicado e retorna false
        // Para que não entre no mesmo espaço duas vezes
        
        $vaga = false;
        $repeticao = false;
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$this->userID}");
        
        // PAROU AQUI: O bug do id 1 está por aqui. Parece ocorrer quando o id do usuário é igual ao id do espaço (Comentário deixado em memória)
        
        foreach ($busca->retornaResultado()[0] as $coluna => $space) {     
            if($coluna!='id' && $space==0){// O resultado da leitura vem como string
                $this->userAvailableColumn = $coluna;
                $vaga = true;
            }
            if($coluna!='id' && $space==$this->id){ // Verificando se o usuário já está no espaço
                $repeticao = true;
            }
        }
        if($vaga && !$repeticao){ // Se encontrou alguma vaga e o usuário não está nesse espaço
            return 'ok';//true;
        } else if (!$vaga && !$repeticao) { // DDDDDDDDDDDDDDDDDDDD 
            return 'limite';//false; // Não encontrou vaga
        } else { // DDDDDDDDDDDDDDDDDDD
            return 'erro'; // As outras duas possibilidades
        }
    }
    
    private function atualizarNumeroUsuarios($sinal) {
        $busca = new read;
        $busca->fazerBusca('SELECT nusers FROM spaces WHERE id = :bv',"bv={$this->id}");
        $nusersAntes = (int) $busca->retornaResultado()[0]['nusers'];
        $nusersDepois = ($sinal=='mais')?$nusersAntes + 1:$nusersAntes - 1;
        if($nusersDepois>0){ // Verificar se é possível reproveitar o objeto $atualizacao do método registrarEntradaUsuario()
            $atualizacao2 = new update();
            $atualizacao2->fazerAtualizacao('spaces',array('nusers'=>'dummy'),"id={$this->id}","nusers={$nusersDepois}");
            if($atualizacao2->retornaResultado()){
                return true;
            } else {
                return false;
            }       
        } else {
            if($this->limparEspaco()){
                // Flag para informar o clientSide que o espaço foi fechado (último usuário saiu)
                $this->emptySpace = true;
                return true;
            } else {
                return false;
            }
        }
    }
    
    private function limparEspaco() {
        $zero = 0;
        $atualizacao3 = new update();
        $atualizacao3->fazerAtualizacao('spaces',array('name'=>'dummy','nusers'=>'dummy','creator_fbuid'=>'dummy','creator_name'=>'dummy','creation_date'=>'dummy','status'=>'dummy','visible'=>'dummy'),"id={$this->id}","name=null&nusers={$zero}&creator_fbuid=null&creator_name=null&creation_date={$zero}&visible=yes&status=off");
        if($atualizacao3->retornaResultado()){
            return true;
        } else {
            return false;
        }
    }
    
    private function verificarRegistroUsuario() {
        $registro = false;
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$this->userID}");
        foreach ($busca->retornaResultado()[0] as $coluna => $space) {     
            if($coluna!='id' && $space==$this->id){ // Verificando se o usuário está no espaço
                $registro = true;
            }
        }
        if($registro){ // Se o usuário foi registrado nesse espaço
            return true;
        } else { // Se não foi
            return false;
        }
    }   
}
