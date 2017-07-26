<?php

/**
 * Description of space
 *
 * @author JoaoCarlosNereu
 */
class space {
    
    private $name;
    private $id;
    private $nUsers;
    private $status;
    private $userID;
    private $userAvailableColumn;


    public function alocarEspaco($name) {
        $this->name = $name;
        require_once '../config/loadConn.inc.php';// Saindo da home
        if($this->procurarVaga()) {
            if($this->reciclarVaga()){
                return true; // Já alocou espaço em uma vaga existente
            } else {
                return false; // Msg: Algo deu errado. Tentar de novo.
            }
        } else { // Se não tinha vaga
            if($this->criarVaga()){
                //echo ' rastro do true';
                return true; // Já criou uma nova vaga e alocou espaço
            } else {
                return false; // Msg: Algo deu errado. Tentar de novo.
            }
        }
    }
    
    // Método utilizado ao entrar no espaço por uma busca
    public function registrarEntradaUsuario($idUsuario,$idEspaco) {
        $this->userID = $idUsuario;
        $this->id = $idEspaco;
        require_once '../config/loadConn.inc.php';// Saindo da home
        if($this->procurarVagaUsuario()){
            $atualizacao = new update();
            $atualizacao->fazerAtualizacao('userspaces',array("{$this->userAvailableColumn}"=>'dummy'),"id={$this->userID}","$this->userAvailableColumn"."={$this->id}");
        } else {
            // Msg: Usuário já está no limite de espaços (10). Deve sair de um para entrar em outro
            return false;
        }
    }
    
    public function registrarSaidaUsuario($idUsuario,$idEspaco) {
        $this->userID = $idUsuario;
        $this->id = $idEspaco;
        $zero = 0;
        require_once '../config/loadConn.inc.php';// Saindo da home
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$this->userID}");
        foreach ($busca->retornaResultado() as $coluna => $space){
            if($space==$this->id){
                $colunaEspaco = $coluna;
            }
        }
        $atualizacao = new update();
        $atualizacao->fazerAtualizacao('userspaces',array("{$colunaEspaco}"=>'dummy'),"id={$this->userID}","$colunaEspaco"."={$zero}");
    }
    
    public function limparEspaco($idEspaco) {
        $this->id = $idEspaco;
        $zero = 0;
        require_once '../loadConn.inc.php';// Saindo do fecharEspaco na pasta ajax
        $atualizacao = new update();
        $atualizacao->fazerAtualizacao('spaces',array('name'=>'dummy','nusers'=>'dummy','status'=>'dummy'),"id={$this->id}","name=null&nusers={$zero}&status=off");
        if($atualizacao->retornaResultado()){
            return true;
        } else {
            return false;
        }
        
    }
    
    public function contarUsuarios($idEspaco) {
        // Método necessário para mostrar o número de usuários em um espaço no resultado da busca
        // Para decidir entre apenas registrar saída ou limpar o espçao ao clicar em sair
    }
    
    public function pegarIDespaco() {
        return $this->id;
    }
    
    public function pegarNomeEspaco($idEspaco) {
        $this->id = $idEspaco;
        require_once '../config/loadConn.inc.php';// Saindo da home
        $busca = new read();
        $busca->fazerBusca('SELECT name FROM spaces WHERE id = :bv',"bv={$this->id}");
        if(!empty($busca->retornaResultado()[0]['name'])){
            return $busca->retornaResultado()[0]['name'];
        } else {
            return 'Erro: Espaço não encontrado.';
        }
        
    }
    // Métodos privados
    
    private function procurarVaga() {
        $off = 'off';
        $busca = new read();
        $busca->fazerBusca('SELECT id FROM spaces WHERE status = :bv LIMIT 1',"bv={$off}");
        if(!empty($busca->retornaResultado()[0]['id'])){
            $this->id = $busca->retornaResultado()[0]['id'];
            return true;
        } else {
            return false;
        }
    }
    
    private function reciclarVaga() {
        $one = 1;
        $on = 'on';
        $atualizacao = new update();
        $atualizacao->fazerAtualizacao('spaces',array('name'=>'dummy','nusers'=>'dummy','status'=>'dummy'),"id={$this->id}","name={$this->name}&nusers={$one}&status={$on}");
        if($atualizacao->retornaResultado()){
            return true;
        } else {
            return false; // Se algo deu errado no caminho e a atualização não foi realizada
        }
    }
    
    private function criarVaga() {
        $one = 1;
        $insercao = new create();
        $insercao->fazerInsercao('spaces',array('name'=>$this->name,'nusers'=>$one,'status'=>'on'));
        if($insercao->retornaResultado()){
            $this->id = $insercao->retornaIDinserido(); // Pegando o novo ID 
            //echo 'Vaga criada de novo.';
            return true;
        } else {
            return false; // Se algo deu errado no caminho e a inserção não foi bem sucedida
        }
    }
    
    private function procurarVagaUsuario() {
        $vaga = false;
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM userspaces WHERE id = :bv',"bv={$this->userID}");
        foreach ($busca->retornaResultado() as $coluna => $space) {
            if($space==0){
                $this->userAvailableColumn = $coluna;
                $vaga = true;
            }
        }
        if($vaga){ // Se encontrou alguma vaga
            return true;
        } else { // Se não encontrou
            return false;
        }
    }
    
}
