<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * Manage class
 * By AlFariz
 *
 *
 *
 *
 * Copyright (C) 2015 by AlFariz
 * Efendi Hariyadi
 *
*/
class Manage extends CI_Controller
{
    var $jmlRecordPeraturan ;
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library(array('session','encryption','Authentification_Lib'));
        $this->load->database();
        setlocale(LC_TIME, 'id_ID');        
        date_default_timezone_set('Asia/Makassar');
    }
    public function index() {
        $this->load->library('Authentification_Lib','authentification_lib');
        if (!$this->authentification_lib->was_login()) {
            redirect('manage/login');
        }
        $data['title_page'] = 'Login Pengguna';
        $data['base_uri'] = base_url();
        $this->load->view('manage/dashboard', $data);        
    }
    
    public function login_process() {
        $is_ajax = $this->input->is_ajax_request();
        $this->output->enable_profiler(TRUE);
        $this->load->library(array('form_validation'));
        $this->form_validation->set_rules('email', 'Email Login', 'trim|required');
        $this->form_validation->set_rules('password', 'login password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            redirect('/manage/login');
        } 
        else {
            $this->load->library('Authentification_Lib');
            $uname = $this->input->post('email');
            $pass = $this->input->post('password');
            if ($is_ajax) {                
                //if user login via ajax
                if ($this->authentification_lib->cek_login($uname, $pass) === TRUE) {
                    $data_js = array('login' => TRUE, 'message' => 'succesfull login');
                    echo json_encode($data_js);
                    
                } 
                else {
                    $data_js = array('login' => FALSE, 'message' => 'login fail maybe wrong username or password input');
                    echo json_encode($data_js);                          
                }
            } 
            else {                
                //if user login post http normally
                if ($this->authentification_lib->cek_login($uname, $pass) === TRUE) {
                    $this->session->set_flashdata(array('login' => FALSE, 'message' => 'login fail maybe wrong username or password input'));
                    redirect('/manage');                                        
                } 
                else {
                    redirect('manage/login');
                    $this->session->set_flashdata('message', 'login fail maybe wrong username or password input');
                    $this->session->set_flashdata('login', FALSE);
                }
            }
        }
    }
    public function logout() {        
        $this->load->library('Authentification_Lib');
        $this->authentification_lib->proses_logout();
        redirect('manage/login');        
    }
    public function login() {        
        if (!$this->authentification_lib->was_login()) {
            $data['title_page'] = 'Halaman Login';
            $data['base_uri'] = base_url();
            $this->load->view('manage/login', $data);
        } 
        else {
            redirect('/manage');
        }
    }
}
/* End of file  */
/* Location: ./application/controllers/Manage.php */
