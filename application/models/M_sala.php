<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_sala extends CI_Model
{
    /*
    0 = Errode exceção
    1 = Operação realizada com sucesso
    8 = Houve algum problema de inserção, atualização, consulta ou exclusão
    9 = Sala desativada no sistema
    10 - Sala já cadastrada no sistema
    98 - método auxiliar de consulta que não trouxe dados */

    public function inserir($codigo, $descricao, $andar, $capacidade){
        try {
            //Verifica se a aula já foi cadastrada
            $retornoConsulta = $this->consultaSala($codigo);

            if($retornoConsulta['codigo'] !=9 &&
                $retornoConsulta['codigo'] !=10) {
            //Query de inserção
            $this->db->query("insert into tbl_sala (codigo, descricao, andar, capacidade) values ($codigo, '$descricao', $andar, $capacidade)");

            //Verificar se a inserção ocorreu com sucesso
            if ($this->db->affected_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Sala cadastrada com sucesso'
                );

            }else {
                $dados = array(
                    'codigo' => 8,
                    'msg' => 'Houve um problema na inserção de salas.'
                );
            }
        }else {
            $dados = array('codigo' => $retornoConsulta['codigo'],
                            'msg' => $retornoConsulta['msg']);
        }
    }catch (Exception $e) {
        $dados = array(
            'codigo' => 0,
            'msg' => 'ATENÇÃO: o seguinte erro ocorreu -> ' . $e->getMessage()
        );
    }

    //Envia o array $dados com as informações tratadas
    //acima pela estrutura de decisão if
    return $dados;
}

    //Metodo privado, será auxiliar na classe
    private function consultaSala($codigo) {
        try {
            //Query para consultar dados de acordo com parâmetros passados
            $sql = "select * from tbl_sala where codigo = $codigo";

            $retornoSala = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if($retornoSala->num_rows() > 0) {
                $linha = $retornoSala->row();
                if(trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' =>9,
                        'msg' => 'Sala desativada no sistema, caso precise reativar fale com o administrador.'
                    );
                }else {
                    $dados = array(
                        'codigo' =>10,
                        'msg' => 'Sala já cadastrada no sistema.'
                    );
                }

            }else {
                $dados = array(
                    'codigo' => 98,
                    'msg' => 'Sala não encontrada no sistema.'
                );
            }
        }catch (Exception $e) {
            $dados = array(
                'codigo' => 0,
                'msg' => 'ATENÇÃO: o seguinte erro ocorreu -> ' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas
        //acima pela estrutura de decisão if
        return $dados;
    }
}
?>