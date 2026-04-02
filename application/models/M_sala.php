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

    public function consultar($codigo, $descricao, $andar, $capacidade) {
        try {
            //Query para consultar dados de acordo com parametros passados
            $sql = "select * from tbl_sala where estatus = '' ";

            if (trim($codigo) != '') {
                $sql = $sql . "and codigo = $codigo ";
            }

            if (trim($andar) != '') {
                $sql = $sql . "and andar = '$andar' ";
            }

            if (trim($descricao) != '') {
                $sql = $sql . "and descricao like '%$descricao%' ";
            }

            if (trim($capacidade) != '') {
                $sql = $sql . "and capacidade = $capacidade ";
            }

            $sql = $sql . "order by codigo";

            $retorno = $this->db->query($sql);

            //Verifica se a consulta ocorreu com sucesso
            if ($retorno->num_rows() > 0) {
                $dados = array(
                    'codigo' => 1,
                    'msg' => 'Consulta efetuada com sucesso.',
                    'dados' => $retorno->result()
                );
            }else {
                $dados = array(
                    'codigo' => 11,
                    'msg' => 'Sala não encontrada.'
                );
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function alterar($codigo, $descricao, $andar, $capacidade) {
        try {
            //Verifica se a sala está cadastrada
            $retornoConsulta= $this->consultaSala($codigo);

            if ($retornoConsulta['codigo'] ==10) {
                //Inico a query para atualizaçõa
                $query = "update tbl_sala set";

                //comparção
                if ($descricao !== '') {
                    $query = "descricao = '$descricao', ";
                }

                if ($descricao !== '') {
                    $query = "andar = $andar, ";
                }

                if ($descricao !== '') {
                    $query = "capacidade = $capacidade, ";
                }

                //Termino a concatenação da query
                $queryFinal = rtrim($query, ",") . "where codigo = $codigo";

                //Executo a Query de atualização dos dados
                $this->db->query($queryFinal);

                //Verificar se atualização teve sucesso
                if ($this->db->affected_rows() > 0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Sala atualizada corretamente.'
                    );

                }else {
                    $dados = array(
                        'codigo' => 8,
                        'msg' => 'Houve algum problema na autorização na tabela de sala.'
                    );
                }
            }else {
                $dados = array('codigo' => $retornoConsulta['codigo'],
                                'msg' => $retornoConsulta['msg']);
            }
        }catch (Exception $e) {
            $dados = array(
                'codigo' =>00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }

    public function desativar($codigo) {
        try {
            $retornoConsulta = $this->consultaSala($codigo);

            if ($retornoConsulta['codigo'] == 10) {

                //Query de atualização de dados
                $this->db->query("update tbl_sala set estatus = 'D' where codigo = $codigo");

                //Verifica se atualizou
                if ($this->db->affected_rows() >0) {
                    $dados = array(
                        'codigo' => 1,
                        'msg' => 'Sala DESATIVADA corretamente.'
                    );

                }else {
                    $dados = array(
                        'codigo' => 8,
                        'msg' => 'Houve algum problema na DESATIVAÇÃO da Sala.'
                    );
                }
            }else {
                $dados = array('codigo' => $retornoConsulta['codigo'],
                                'msg' => $retornoConsulta['msg']);
            }
        }catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg'=> 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array de dados com as informações tratadas acima pela estrutura de decisão if
        return $dados;
    }
}
?>