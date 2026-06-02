<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_professor extends CI_Model {
    /*
    Tipos de retorno das validações (codigo de erro)
    0 - Erro de exceção
    1 - Operação realizada no banco de dados com sucesso (Inserção, Alteração, Consulta ou exclusão)
    8 - Houve algum problema de inserção, atualização, consulta ou exclusão
    9 - Professor desativado no sistema
    10 - Professor já cadastrado
    11 - Professor não encontrado pelo método publico
    98 - Método auxiliar de consulta que não trouxe dados
    */

    public function inserir($nome, $cpf, $tipo) {
        try {
            //Verifico se o professor já está cadastrado
            $retornoConsulta = $this->consultaProfessorCpf($cpf);

            if($retornoConsulta['codigo'] != 9 &&
            $retornoConsulta['codigo'] != 10) {
                //Query de inserção dos dados
                $this->db->query("insert into tbl_professor (nome, tipo, cpf) values ('$nome', '$tipo', '$cpf')");

                //Verificar se a inserção ocorreu com sucesso
                if($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor cadastrado corretamente.');
                } else {
                    $dados = array('codigo' => 8,
                                    'msg' => 'Houve algum problema na inserção na tabela de professor.');
                }
            } else {
                $dados = array('codigo' => $retornoConsulta['codigo'],
                                'msg' => $retornoConsulta['msg']);
            }
        }catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas acima pelo if
        return $dados;
    }

    private function consultaProfessorCpf($cpf) {
        try {
            //Query para consultar dados de acordo com parametros passados
            $sql = "select * from tbl_professor where cpf = '$cpf'";

            $retornoProfessor = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if($retornoProfessor->num_rows() > 0) {
                $linha = $retornoProfessor->row();
                if(trim($linha->estatus) == "D") {
                    $dados = array(
                        'codigo' => 9,
                        'msg' => 'Professor desativado no sistema, caso precise reativar a mesma, fale com o ADM.'
                    );
                }else {
                    $dados =array(
                        'codigo' => 10,
                        'msg' => ' Professor jé cadastrado no sistema.');
                }
            } else {
                $dados = array('codigo' => 98,
                'msg' => 'Professor não encontrado');
            }
        }catch (Exception $e) {
            $dados =array(
                'codigo' => 00,
                'msg' => 'ATENÇÂO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        //Envia o array $dados com as informações tratadas acima pelo if
        return $dados;
    }

    private function consultaProfessorCod($codigo) {
        try {
            //Query para consultar dados de acordo com parametros passados
            $sql =  "select * from tbl_professor where codigo = '$codigo' and estatus = ''";

            $retornoProfessor =$this->db->query($sql);
            //Verificar se a consulta ocorreu com sucesso
            if($retornoProfessor->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso. ');
            } else {
                $dados = array('codigo' => 98,
                                'msg' => 'Professor não encontrado.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }

        //Envia o array $dados com as informações tratadas acima pelo if
        return $dados;
    }
    
    public function consultar($codigo, $nome, $cpf, $tipo) {
        try{
            //Query para consultaar dados de acordo com parametros passados
            $sql = "select * from tbl_professor where estatus = '' ";

            if(trim($codigo) != '') {
            $sql = $sql . "and codigo = $codigo ";
            }

            if(trim($cpf) != '') {
            $sql = $sql . "and cpf = '$cpf' ";
            }

            if(trim($nome) != '') {
            $sql = $sql . "and nome like  '%$nome%' ";
            }

            if(trim($tipo) != '') {
            $sql = $sql . "and tipo = '$tipo' ";
            }

            $sql = $sql . " order by nome";

            $retorno = $this->db->query($sql);

            //Verificar se a consulta ocorreu com sucesso
            if($retorno->num_rows() > 0) {
                $dados = array('codigo' => 1,
                                'msg' => 'Consulta efetuada com sucesso. ',
                                'dados' => $retorno->result());
            } else {
                $dados = array('codigo' => 11,
                                'msg'=> 'Professor não consultado.');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu ->' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura if
        return $dados;
    }

    public function alterar($codigo, $nome, $cpf, $tipo) {
        try {
            //Verifico se o professor está cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if($retornoConsulta['codigo'] == 1) {
                //Inicio a query para atualização
                $query = "update tbl_professor set";

                //Vamos comparar os itens
                if($nome !== '') {
                    $query .= "nome = '$nome', ";
                }

                if($cpf !== '') {
                    $query .= "cpf = '$cpf', ";
                }

                if($tipo !== '') {
                    $query .= "tipo = '$tipo', ";
                }
                //Termino a concatenação da query
                $queryFinal = rtrim($query, ", ") . "where codigo = $codigo";

                //Executo a QUery de atualização de dados
                $this->db->query($queryFinal);

                //Verificar se a atualização ocorreu com sucesso
                if($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor atualizado corretamente.');
                } else {
                    $dados = array('codigo' => 8,
                                    'msg' => 'Houve algum problema na atualização na table de professor.');
                }
            } else {
                $dados = array('codigo' => $retornoConsulta['codigo'],
                                'msg' => $retornoConsulta['msg']);
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas acima pela estrutura if
        return $dados;
    }

    public function desativar($codigo) {
        try {
            //Verifico se o professor está cadastrado
            $retornoConsulta = $this->consultaProfessorCod($codigo);

            if($retornoConsulta['codigo'] == 1) {

                //Query de atualização dos dados
                $this->db->query("update tbl_professor set estatus = 'D' where codigo = $codigo");

                //Verificar se a atualização ocorreu com sucesso
                if($this->db->affected_rows() > 0) {
                    $dados = array('codigo' => 1,
                                    'msg' => 'Professor DESATIVADO corretamente.');
                } else {
                    $dados = array('codigo' => 5,
                                    'msg' => 'Houve algum problema na DESATIVAÇÃO do Professor.');
                }

            }else {
                $dados = array('codigo' => 6,
                                'msg' => 'Professor não cadastrado no Sistema, não pode excluir');
            }
        } catch (Exception $e) {
            $dados = array(
                'codigo' => 00,
                'msg' => 'ATENÇÃO: O seguinte erro aconteceu -> ' . $e->getMessage()
            );
        }
        //Envia o array $dados com as informações tratadas acima pelo IF
        return $dados;
    }
}