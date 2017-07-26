<?php


/**
 * Description of message
 *
 * @author JoaoCarlosNereu
 */
class message {
    
    private $texto;
    private $mensagem;


    public function publicarTexto($texto) {
        $this->texto = (String) strip_tags(trim($texto));
        $this->formatarMensagem();
    }
    
    public function pegarMensagem() {
        return $this->mensagem;
    }
    
    /* MÉTODOS PRIVADOS*/
    
    private function formatarMensagem() {
        //$this->mensagem = 'menino no farol';
        $this->mensagem = "<div class=\"espaco_moldura_mensagem\">{$this->texto}</div>";
    }
    
}
