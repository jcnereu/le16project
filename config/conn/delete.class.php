<?php

/**
 * Description of delete:
 *
 * @author JoaoCarlosNereu
 */
class delete extends conn{
    
    private $tabela;
    private $linhas;
    private $bindValues;
    private $resultado;
    private $conexao;
    private $query;
    private $statement;
    
    /**
     * <b>fazerExclusao</b>Executa a exclusão de linhas no banco de dados.
     * @param String $tabela : Informe o nome da tabela no banco.
     * @param String $linhas : Linhas a serem excluídas em formato bind value, ex: "id" = :bv
     * @param String $bindValues : valor real do parâmetro informado em $linhas, ex: bv={$this->ID}
     */
    public function fazerExclusao($tabela, $linhas, $bindValues){
        $this->tabela = (String) $tabela;
        $this->linhas = (String) $linhas;
        // Convertendo string em array
        parse_str($bindValues, $this->bindValues);
        $this->executarExclusao();
    }
    
    /**
     * <b>retornaResultado</b> Retorna um booleano
     * @return TRUE ou FALSE dependendo do sucesso da exclusão
     */
    public function retornaResultado() {
        return $this->resultado;
    }
    
    // MÉTODOS PRIVADOS
    
     private function montarQuery() {
        $this->query = "DELETE FROM {$this->tabela} WHERE {$this->linhas}";
    }
    
    private function prepararStatement() {
        $this->conexao = parent::pegarConexao();
        $this->statement= $this->conexao->prepare($this->query);
    }
    
    private function executarExclusao() {
        $this->montarQuery();
        $this->prepararStatement();
        try {
            $this->statement->execute($this->bindValues);
            $this->resultado = true;
        } catch (PDOException $ex) {
            $this->resultado = false;
            msgSistema("<b>Erro ao fazer a exclusão no banco de dados</b>:{$ex->getMessage()}",$ex->getCode());
            die;
        }
    }
}
