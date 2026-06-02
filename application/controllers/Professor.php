<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professor extends CI_Controller {
    /*
    Validação dos retornos (Código de erro)
    1 - Operação realizada no banco de dados com sucesso (Inserção, Alteração, Consulta ou Exclusão)
    2 - Conteudo passado nulo ou vazio
    3 - Conteudo zerado
    4- Conteudo não inteiro
    5 - Conteudo não é um texto
    6 - Data em formato inválido
    12 - Na atualização, peloo menos um atributo deve ser passado
    15 - CPF com menos de 11 digitos
    16 - CPF com todos os digitos iguais
    17 - CPF com digitos verificadores incorretos
    99 - Parâmetros passados do front não correspondem ao método
    */

    //Atributo para acessar o model
    public M_professor $M_professor;

    //Atributos privados da classe
    private $codigo;
    private $nome;
    private $cpf;
    private $tipo;
    private $estatus;
    
    //Getter dos Atributos
    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getCpf()
    {
        return $this->cpf;
    }

    public function getTipo()
    {
        return $this->tipo;
    
        }
    public function getEstatus()
    {
        return $this->estatus;
    }

    //Setters dos atributos
    public function setCodigo($codigoFront)
    {
        $this->codigo = $codigoFront;
    }

    public function setNome($nomeFront)
    {
        $this->nome = $nomeFront;
    }

    public function setCpf($cpfFront)
    {
        $this->cpf = $cpfFront;
    }

    public function setTipo($tipoFront)
    {
        $this->tipo = $tipoFront;
    }

    public function setEstatus($estatusFront)
    {
        $this->estatus = $estatusFront;
    }

    public function inserir() {

        // Carrega a helper
        $this->load->helper('geral');

        //Atributos para controlar o status do método
        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta do front (Helper)
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no front.'];
            } else {
                //Validar campos quanto ao tipo de dados e tamanho (Helper)
                $retornoNome = validarDados($resultado->nome, 'string', true);
                $retornoCPF = validarDados($resultado->cpf, 'string', true);
                $retornoCPFNroValido = validarCPF($resultado->cpf);
                $retornoTipo = validarDados($resultado->tipo, 'string', true);

                if($retornoNome['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoNome['codigoHelper'],
                                'campo' => 'Nome',
                                'msg' => $retornoNome['msg']];
                }

                if($retornoCPF['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCPF['codigoHelper'],
                                'campo' => 'CPF',
                                'msg' => $retornoCPF['msg']];
                }

                if($retornoCPFNroValido['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCPFNroValido['codigoHelper'],
                                'campo' => 'CPF validação numero',
                                'msg' => $retornoCPFNroValido['msg']];
                }

                if($retornoTipo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoTipo['codigoHelper'],
                                'campo' => 'Tipo',
                                'msg' => $retornoTipo['msg']];
                }

                //Se não encontrar erros
                if (empty($erros)) {
                    $this->setNome($resultado->nome);
                    $this->setCpf($resultado->cpf);
                    $this->setTipo($resultado->tipo);

                    $this->load->model('M_professor');
                    $resBanco = $this->M_professor->inserir(
                        $this->getNome(),
                        $this->getCpf(),
                        $this->getTipo()
                    );

                    if ($resBanco['codigo']== 1) {
                        $sucesso = true;
                    } else {
                        //Captura erro de banco
                        $erros [] = [
                            'codigo' => $resBanco['codigo'],
                            'msg' => $resBanco['msg']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        //Retorrno unico
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, ' erros' => $erros];
        }

        //Trasnforma o array em JSON
        echo json_encode($retorno);
    }

    public function consultar () {

        // Carrega a helper
        $this->load->helper('geral');

        //Atributos para controlar o status do método
        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0',
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta no front (Helper)
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
            } else {
                //Validar campos quanto ao tipo de dado e tamanho (Helper)
                $retornoCodigo = validarDadosConsulta($resultado->codigo, 'int');
                $retornoNome = validarDadosConsulta($resultado->nome, 'string');
                $retornoCPF = validarDadosConsulta($resultado->cpf, 'string');
                $retornoTipo = validarDadosConsulta($resultado->tipo, 'string');

                if($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo' => 'Codigo',
                                'msg' => $retornoCodigo['msg']];
                }

                if($retornoNome['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoNome['codigoHelper'],
                                'campo' => 'Nome',
                                'msg' => $retornoNome['msg']];
                }

                if($retornoCPF['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCPF['codigoHelper'],
                                'campo' => 'CPF',
                                'msg' => $retornoCPF['msg']];
                }

                if($retornoTipo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoTipo['codigoHelper'],
                                'campo' => 'Tipo',
                                'msg' => $retornoTipo['msg']];
                }

                if($resultado->cpf !='') {
                    //CPF informado, verificar se é numero valido
                    $retornoCPFNroValido = validarCPF($resultado->cpf);
                    if($retornoCPFNroValido['codigoHelper'] != 0) {
                        $erros[] = ['codigo' =>$retornoCPFNroValido['codigoHelper'],
                                    'campo' =>'CPF validação número',
                                    'msg' => $retornoCPFNroValido['msg']];
                    }
                }

                //Se não houver erros
                if (empty($erros)) {
                    $this->setCodigo($resultado->codigo);
                    $this->setNome($resultado->nome);
                    $this->setCpf($resultado->cpf);
                    $this->setTipo($resultado->tipo);

                    $this->load->model('M_professor');
                    $resBanco = $this->M_professor->consultar($this->getCodigo(),
                                                            $this->getNome(),
                                                            $this->getCpf(),
                                                            $this->getTipo());

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

        //Retorno unico
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg'],
                        'dados' => $resBanco['dados']];
        } else {
            $retorno = ['sucesso' => $sucesso, ' erros' => $erros];
        }

        //Transforma o array em JSON
        echo json_encode($retorno);
    }

    public function alterar () {

        // Carrega a helper
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0',
                "nome" => '0',
                "cpf" => '0',
                "tipo" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta do front
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no Front.'];
            }else {
                //Pelo menos um parametro precisa ter dados para ocorrer a atualização
                if(trim($resultado->nome) == '' && trim($resultado->cpf) =='' && trim($resultado->tipo) =='') {
                    $erros[] = ['codigo' => 12,
                                'msg'=> 'Pelo menos um parametro precisa ser passado para atualização'];
                } else {
                    //Validar campos quanto ao tipo de dado e tamanho (Helper)
                    $retornoCodigo = validarDados($resultado->codigo, 'int', true);
                    $retornoNome = validarDadosConsulta($resultado->nome, 'string');
                    $retornoCPF = validarDadosConsulta($resultado->cpf, 'string');
                    $retornoTipo = validarDadosConsulta($resultado->tipo, 'string');

                    if($retornoCodigo['codigoHelper'] != 0) {
                        $erros[] = ['codigo' =>$retornoCodigo['codigoHelper'],
                                    'campo' => 'Codigo',
                                    'msg' => $retornoCodigo['msg']];
                    }

                    if($retornoNome['codigoHelper'] != 0) {
                        $erros[] = ['codigo' =>$retornoNome['codigoHelper'],
                                    'campo' => 'Nome',
                                    'msg' => $retornoNome['msg']];
                    }

                    if($retornoCPF['codigoHelper'] != 0) {
                        $erros[] = ['codigo' =>$retornoCPF['codigoHelper'],
                                    'campo' => 'CPF',
                                    'msg' => $retornoCPF['msg']];
                    }

                    if($retornoTipo['codigoHelper'] != 0) {
                        $erros[] = ['codigo' =>$retornoTipo['codigoHelper'],
                                    'campo' => 'Tipo',
                                    'msg' => $retornoTipo['msg']];
                    }

                    if($resultado->cpf !='') {
                        //CPF informado, verificar se é numero valido
                        $retornoCPFNroValido = validarCPF($resultado->cpf);
                        if($retornoCPFNroValido['codigoHelper']!= 0) {
                            $erros[] = ['codigo' => $retornoCPFNroValido['codigoHelper'],
                                        'campo' => 'CPF validação número',
                                        'msg' => $retornoCPFNroValido['msg']];
                        }
                    }

                    //Se não houver erros 
                    if (empty($erros)) {
                        $this->setCodigo($resultado->codigo);
                        $this->setNome($resultado->nome);
                        $this->setCpf($resultado->cpf);
                        $this->setTipo($resultado->tipo);

                        $this->load->model('M_professor');
                        $resBanco = $this->M_professor->alterar($this->getCodigo(),
                                                                $this->getNome(),
                                                                $this->getCpf(),
                                                                $this->getTipo());

                        if ($resBanco['codigo'] == 1) {
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
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado: ' . $e->getMessage()];
        }

        //Retorno Unico
        if ($sucesso == true) {
            $retorno = ['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucesso' => $sucesso, 'erros' => $erros];
        }

        // Transforma o array em JSON
        echo json_encode($retorno);
    }

    public function desativar () {

        // Carrega a helper
        $this->load->helper('geral');

        $erros = [];
        $sucesso = false;

        try {

            $json = file_get_contents('php://input');
            $resultado = json_decode($json);
            $lista = [
                "codigo" => '0'
            ];

            if (verificaParametro($resultado, $lista) != 1) {
                //Validar vindos de forma correta do front (Helper)
                $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no Front End.'];
            } else {
                $retornoCodigo = validarDados($resultado->codigo, 'int', true);

                if($retornoCodigo['codigoHelper'] != 0) {
                    $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                                'campo' => 'Codigo',
                                'msg' => $retornoCodigo['msg']];
                }

                //Se não encontrar erros
                if (empty($erros)) {
                    $this->setCodigo($resultado->codigo);

                    $this->load->model('M_professor');
                    $resBanco = $this->M_professor->desativar($this->getCodigo());

                    if ($resBanco['codigo']== 1) {
                        $suceso = true;
                    }else {
                        //Captura erro do banco
                        $erros[] = [
                            'codigo' => $resBanco['codigo'],
                            'msg' => $resBanco['msg']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            $erros[] = ['codigo' => 0, 'msg' => 'Erro inesperado' . $e->getMessage()];
        }

        //Retorno unico
        if($sucesso == true) {
            $retorno =['sucesso' => $sucesso, 'codigo' => $resBanco['codigo'], 'msg' => $resBanco['msg']];
        } else {
            $retorno = ['sucessso' => $sucesso, 'erros' => $erros];
        }

        //Transforma o array em JSON
        echo json_encode($retorno);
    }
}
?>