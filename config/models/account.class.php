<?php

/**
 * Description of conta:
 * Responsável por modelar e prover todos os métodos relacionados à conta do usuário
 * @author JoaoCarlosNereu
 */
class account {
    
    private $email;
    private $password;
    private $confirPassword;

    public function createAccount($email,$password,$confirmPassword) {
        $this->email = (String) strip_tags(trim($email));
        $this->password = $password;
        $this->confirPassword = $confirmPassword;
    }
    // Alerta de segurança na URL: https://goo.gl/zmWq3m.
    //https://stackoverflow.com/questions/6142433/send-a-confirmation-email-using-php
    // Para validar email: https://www.w3schools.com/php/php_form_url_email.asp
    //Tutorial email de confirmação: https://code.tutsplus.com/tutorials/how-to-implement-email-verification-for-new-members--net-3824
}
