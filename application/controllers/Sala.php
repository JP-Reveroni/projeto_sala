<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sala extends CI_Controller {
/*
Retorno das validações(Código de erro)
1- Operação realizada com sucesso
2- Conteudo nulo ou vazio
3- Conteudo zerado
4- Conteudo não inteiro
5- Conteudo não é texto
6- Data em formato invalido
7- Hora em formato invalido
99- Parametros do front não correspondem ao método
*/

//Atributo para acessar o model
public M_sala $M_sala;


//Atributos privados da Classe
private $codigo;
private $descricao;
private $andar;
private $capacidade;
private $estatus;

//Getters dos atributos
public function getCodigo(){
    return $this->codigo;
}

public function getDescricao(){
    return $this->descricao;
}

public function getAndar(){
    return $this->andar;
}

public function getCapacidade(){
    return $this->capacidade;
}

public function getEstatus(){
    return $this->estatus;
}

//Setters dos atributos
public function setCodigo($codigoFront){
    $this->codigo= $codigoFront;
}

public function setDescricao($descricaoFront){
    $this->descricao= $descricaoFront;
}

public function setAndar($andarFront){
    $this->andar= $andarFront;
}

public function setCapacidade($capacidadeFront){
    $this->capacidade= $capacidadeFront;
}

public function setEstatus($estatusFront){
    $this->estatus= $estatusFront;
}

public function inserir() {
    //Atributos para controlar o status do método

    $this->load->helper('geral'); //Carrega Helper para validar os dados

    $erros = [];
    $sucesso= false;

    try {

        $json = file_get_contents('php://input');
        $resultado = json_decode($json);
        $lista = [
            "codigo" => '0',
            "descricao"  => '0',
            "andar"  => '0',
            "capacidade"  => '0'
        ];

        if (verificaParametro($resultado, $lista)!= 1) {
            //Validar vindos de forma correta do frontend (Helper)
            $erros[] = ['codigo' => 99, 'msg' => 'Campos inexistentes ou incorretos no FrontEnd.'];
        } else {
            //Validar campos quanto ao tipo de dado e tamanho (Helper)
            $retornoCodigo = validarDados($resultado->codigo,'int', true);
            $retornoDescricao = validarDados($resultado->descricao,'string', true);
            $retornoAndar = validarDados($resultado->andar,'int', true);
            $retornoCapacidade = validarDados($resultado->capacidade,'int', true);

            if($retornoCodigo['codigoHelper'] != 0){
                $erros[] = ['codigo' => $retornoCodigo['codigoHelper'],
                            'campo' => 'Codigo',
                            'msg' => $retornoCodigo['msg']];
            }

            if($retornoDescricao['codigoHelper'] != 0){
                $erros[] = ['codigo' => $retornoDescricao['codigoHelper'],
                            'campo' => 'Descricao',
                            'msg' => $retornoDescricao['msg']];
            }

            if($retornoAndar['codigoHelper'] != 0){
                $erros[] = ['codigo' => $retornoAndar['codigoHelper'],
                            'campo' => 'Andar',
                            'msg' => $retornoAndar['msg']];
            }
            
            if($retornoCapacidade['codigoHelper'] != 0){
                $erros[] = ['codigo' => $retornoCapacidade['codigoHelper'],
                            'campo' => 'Capacidade',
                            'msg' => $retornoCapacidade['msg']];
            }

            //Se não encontrar erros
            if (empty($erros)) {
                $this->setCodigo($resultado->codigo);
                $this->setDescricao($resultado->descricao);
                $this->setAndar($resultado->andar);
                $this->setCapacidade($resultado->capacidade);

                $this->load->model('M_sala');
                $resBanco = $this->M_sala->inserir(
                    $this->getCodigo(),
                    $this->getDescricao(),
                    $this->getAndar(),
                    $this->getCapacidade()
                );

            if ($resBanco['codigo']==1) {
                $sucesso = true;
                }else{
                    //Captura erro do banco
                    $erros[] =[
                        'codigo' => $resBanco['codigo'],
                        'msg' => $resBanco['msg']
                    ];
                }
            }
        }
    }catch (Exception $e) {
        $erros[] = ['codigo' => 0, 'msg' =>' Erro inesperado: ' . $e->getMessage()];
    }

    //Monta retorno unico
    if($sucesso == true) {
        $retorno = ['sucesso' => $sucesso, 'msg' => 'Sala cadastrada com sucesso.'];
    }else{
        $retorno = ['sucesso' =>$sucesso, 'erros' => $erros];
    }

    //array para json
    echo json_encode($retorno);
    }
}
?>