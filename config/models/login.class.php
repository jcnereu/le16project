<?php

/**
 * Description of login: 
 * Métodos relacionados ao login
 * @author JoaoCarlosNereu
 */
class login {
    
    private $emailForm;
    private $senhaForm;
    private $email;
    private $senha;
    private $nome;
    private $id;
    
    public function fazerLogin($email,$senha) {
        $this->emailForm = (String) strip_tags(trim($email));
        $this->senhaForm = (String) strip_tags(trim($senha));
        if($this->checarEmail()){
            if($this->checarSenha()){
                $this->criarSecao();
                return true;
            }
            else {
                echo 'SENHA INVÁLIDA.';
                return false;
            }
        }
        else {
            echo 'EMAIL NÃO ENCONTRADO';
            return false;
        }
        
    }
    
    public function checarLogin() {
        if(empty($_SESSION['dadosUsuario'])):
            return false;
        else:
            return true;
        endif;
    }
    
    //MÉTODOS PRIVADOS
    
    private function checarEmail() {
        require_once 'config/loadConn.inc.php';// Saindo da index
        $busca = new read();
        $busca->fazerBusca('SELECT * FROM users WHERE email = :bv',"bv={$this->emailForm}");
        if($busca->contaResultados()>0){
            $this->id = $busca->retornaResultado()[0]['id'];
            $this->email = $busca->retornaResultado()[0]['email'];
            $this->senha = $busca->retornaResultado()[0]['password'];
            $this->nome = $busca->retornaResultado()[0]['name'];
            return true;
        }    
        else {
            return false;
        }
    }
    
    private function checarSenha() {
        if($this->senhaForm==$this->senha){
            return true;
        }
        else {
            return false;
        }
    }
    
    private function criarSecao() {
        // Se não houver nenhuma sessão iniciada
        if(!session_id()):
            session_start();
        endif;
        //Pega os dados do usuário e aloca em uma varável global
        $_SESSION['dadosUsuario'] = array('id'=>$this->id,'email'=>$this->email,'senha'=>$this->senha,'nome'=>$this->nome);
    }
}
