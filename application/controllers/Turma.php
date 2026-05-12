<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turma extends CI_Controller {
    /*
    Códigos de erro
    1 - Operação realizada no banco de dados com sucesso (Inserção, Alteração, Consulta ou Exclusão)
    2 - Conteúdo passado nulo ou vazio
    3 - Conteúdo zeerado
    4 - Conteudo não inteiro
    5 - Conteudo não é um texto
    6 - Data em formato invalido
    12 - Na atualização, pelo menos um atributo deve ser passado
    99 - Parâmetros passados não correspondem ao método
    */

    //Atributo para acessar o model
    public M_turma $m_turma;

    //Atributos privados da classe
    private $codigo;
    private $descricao;
    private $capacidade;
    private $dataInicio;
    private $estatus;

    //Getters dos Atributos
    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getCapacidade()
    {
        return $this->capacidade;
    }

    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    public function getEstatus()
    {
        return $this->estatus;
    }

    //Setters dos Atributos
    public function setCodigo($codigoFront)
    {
        $this->codigo = $codigoFront;
    }

    public function setDescricao($descricaoFront)
    {
        $this->descricao = $descricaoFront;
    }

    public function setCapacidade($capacidadeFront)
    {
        $this->capacidade = $capacidadeFront;
    }

    public function setDataInicio($dataInicioFront)
    {
        $this->dataInicio = $dataInicioFront;
    }

    public function setEstatus($estatusFront)
    {
        $this->tipoUsuario = $estatusFront;
    }

    public function inserir(){
        //Atributos para controlar o status de nosso método

        //Carrega Helper para validar os dados
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "descricao" => '0',
                "capacidade" => '0',
                "dataInicio" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                //Validação vindo de forma correta do front (Helper)
                $erro[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {
                //Validar campos quanto ao tipo de dado e tamanho (Helper)
                $retornoDescricao = validarDados($resultado->descricao,'string', true);
                $retornoCapacidade = validarDados($resultado->capacidade,'int', true);
                $retornoDataInicio = validarDados($resultado->dataInicio,'date', true);

                if($retornoDescricao['codigoHelper'] != 0){
                    $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                'campo' => 'Descrição',
                                'msg' => $retornoDescricao['msg']];
                }

                if($retornoCapacidade['codigoHelper'] != 0){
                    $erros[] = ['codigo' => $retornoCapacidade['codigoHelper'],
                                'campo' => 'Capacidade',
                                'msg' => $retornoCapacidade['msg']];
                }

                if($retornoDataInicio['codigoHelper'] != 0){
                    $erros[] = ['codigo' => $retornoDataInicio['codigoHelper'],
                                'campo' => 'Andar',
                                'msg' => $retornoDataInicio['msg']];
                }

                //Se não encontrar erros
                if (empty($erros)) {
                    $this->setDescricao($resultado->descricao);
                    $this->setCapacidade($resultado->capacidade);
                    $this->setDataInicio($resultado->dataInicio);

                    $this->load->model('M_turma');
                    $resBanco = $this->M_turma->inserir(
                        $this->getDescricao(),
                        $this->getCapacidade(),
                        $this->getDataInicio()
                    );

                    if ($resBanco['codigo']== 1) {
                        $sucesso = true;
                    } else {
                        // Captura erro do banco
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg'   => $resBanco['msg']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        //Retorno único
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'ccodigo' => $resBanco['codigo'], 'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        //Transforma o array em JSON
        echo json_encode($retorno);
    }

    public function consultar () {
        //Atributo para controlar o status de nosso método
        
        //Carrega Helper para validar os dados
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0',
                "descricao" => '0',
                "capacidade" => '0',
                "dataInicio" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else{
                //Validar campos quanto ao tipo de dado e tamanho (helper)
                $retornoCodigo =  validarDadosConsulta($resultado->codigo,'int');
                $retornoDescricao =  validarDadosConsulta($resultado->descricao,'string');
                $retornoCapacidade =  validarDadosConsulta($resultado->capacidade,'int');
                $retornoDataInicio =  validarDadosConsulta($resultado->dataInicio,'date');

                if($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo' => 'Codigo',
                                'msg' => $retornoCodigo['msg']];
                }

                if($retornoDescricao['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                'campo' => 'Descricao',
                                'msg' => $retornoDescricao['msg']];
                }

                if($retornoCapacidade['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCapacidade['codigoHelper'],
                                'campo' => 'Capacidade',
                                'msg' => $retornoCapacidade['msg']];
                }

                if($retornoDataInicio['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoDataInicio['codigoHelper'],
                                'campo' => 'Andar',
                                'msg' => $retornoDataInicio['msg']];
                }

                //Se não tiver error_get_last
                if (empty($erros)) {
                    $this->setCodigo($resultado->codigo);
                    $this->setDescricao($resultado->descricao);
                    $this->setCapacidade($resultado->capacidade);
                    $this->setDataInicio($resultado->dataInicio);

                    $this->load->model('M_turma');
                    $resBanco = $this->M_turma->consultar($this->getCodigo(),
                                                          $this->getDescricao(),
                                                          $this->getCapacidade(),
                                                          $this->getDataInicio());
                    
                    if($resBanco['codigo']==1 ) {
                        $sucesso = true;
                    } else {
                        // Captura erro do banco
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg' => $resBanco['msg']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' .$e->getMessage()];
        }
        //Retorno
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg'],
                        'dados' => $resBanco['dados']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        //Transformando o array em JSON
        echo json_encode($retorno);
    }

    public function alterar() {
        //Atributos para controlar o status do método

        //Carrega Helper para validar os dados
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0',
                "descricao" => '0',
                "capacidade" => '0',
                "dataInicio" => '0'
            ];

            if(verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta do front (Helper)
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else{
                //Pelo menos um dos três parametros precisam ter dados para atualizar
                if(trim($resultado->descricao) == '' && trim($resultado->capacidade) == '' && trim($resultado->dataInicio)== '') {
                    $erros[] = ['codigo' => 12,
                                'msg' => 'Pelo menos um parametro precisa ser passado para atualização acontecer'];
                } else{
                        //Validar campos quanto ao tipo de dado e tamanho (heler)
                        $retornoCodigo = validarDados($resultado->codigo, 'int', true);
                        $retornoDescricao = validarDados($resultado->descricao, 'string');
                        $retornoCapacidade = validarDados($resultado->capacidade, 'int');
                        $retornoDataInicio = validarDados($resultado->dataInicio, 'date');

                        if($retornoCodigo['codigoHelper'] != 0) {
                            $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                        'campo' => 'Codigo',
                                        'msg' => $retornoCodigo['msg']];
                        }

                        if($retornoDescricao['codigoHelper'] != 0) {
                            $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                                        'campo' => 'Descrição',
                                        'msg' => $retornoDescricao['msg']];
                        }

                        if($retornoCapacidade['codigoHelper'] != 0) {
                            $erros[] = ['codigo' => $retornoCapacidade['codigoHelper'],
                                        'campo' => 'Capacidade',
                                        'msg' => $retornoCapacidade['msg']];
                        }

                        if($retornoDataInicio['codigoHelper'] != 0) {
                            $erros[] = ['codigo' => $retornoDataInicio['codigoHelper'],
                                        'campo' => 'Data Inicio',
                                        'msg' => $retornoDataInicio['msg']];
                        }
                        
                        //Se não encontrar erros
                        if (empty($erros)) {
                            $this->setCodigo($resultado->codigo);
                            $this->setDescricao($resultado->descricao);
                            $this->setCapacidade($resultado->capacidade);
                            $this->setDataInicio($resultado->dataInicio);

                            $this->load->model('M_turma');
                            $resBanco = $this->M_turma->alterar($this->getCodigo(),
                                                                $this->getDescricao(),
                                                                $this->getCapacidade(),
                                                                $this->getDataInicio());

                        if ($resBanco['codigo']== 1) {
                            $sucesso = true;
                        } else {
                            //Captura de erro do banco
                            $erros[] = [
                                'codigo' => $resBanco['codigo'],
                                'msg' => $resBanco['msg']
                            ];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado ' .$e->getMessage()];
        }

        //Retorno
        if ($sucesso == true) {
            $retorno = ['suceso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        //Transforma o array em JSON
        echo json_encode($retorno);
    }

    public function desativar() {
        //Atributoa para controlar o status de nosso método

        //Carrega Helper para validar os dados
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0'
            ];

            if(verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta do frontEnd (Helper)
                $erros[] = ['codigo' => 99, ' msg' => 'Campos inexistentes ou incorretos noFrontEnd.'];
            } else {
                // Validar código quanto ao tipo de dado e tamanho (Helper)
                $retornoCodigo = validarDados($resultado->codigo,'int', true);

                if($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo' => 'Codigo',
                                'msg' => $retornoCodigo['msg']];
                }

                //Se não houver erros
                if(empty($erros)) {
                    $this->setCodigo($resultado->codigo);

                    $this->load->model('M_turma');
                    $resBanco = $this->M_turma->desativar($this->getCodigo());

                    if ($resBanco['codigo']== 1) {
                        $sucesso = true;
                    } else {
                        //Captura erro do banco
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg' => $resBanco['msg']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        //Monta retorno único
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        //Transforma o array em JSON
        echo json_encode($retorno);
    }
}
?>