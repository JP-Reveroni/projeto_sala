<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Funcoes extends CI_Controller {

    public function index() {
        $this->load->helper('url'); // Carrega a helper
        $this->load->view('login');
    }

    public function indexPagina() {
        $this->load->view('Index');
    }

    public function encerrarSistema() {
        $this->load->helper('url');
        //Volta para a pagina de login
        redirect('Funcoes/index');
    }
}