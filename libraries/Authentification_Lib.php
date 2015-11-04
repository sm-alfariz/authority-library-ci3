<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of AuthentificationLib
 * 10/07/2105 Samarinda
 * @author efendi hariyadi
 */
class Authentification_Lib
{
    
    protected $CI;         
    public function __construct() {
        
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();
        $this->CI->load->library('encryption');
        $this->CI->encryption->initialize(array('cipher' => 'aes-256', 'mode' => 'cbc', 'key' => $this->CI->config->item('encryption_key')));
        $this->CI->load->database();
        $this->CI->load->library('session');
        setlocale(LC_TIME, 'id_ID');
        date_default_timezone_set('Asia/Makassar');
    }
    
    public function generate_hash_pass($input_pass) {
        return $this->CI->encryption->encrypt($input_pass);
    }
    
    public function cek_password($input_pass, $db_pass) {
        $in_pass = $this->CI->encryption->decrypt($this->CI->encryption->encrypt($input_pass));
        if ($in_pass === $this->CI->encryption->decrypt($db_pass)) {
            return TRUE;
        } 
        else {
            return FALSE;
        }
    }
    
    private function getIs_login() {
        return $this->CI->session->userdata('logged_in');
    }
    
    public function was_login() {
        if ($this->getIs_login() === TRUE) {
            return TRUE;
        } 
        else {
            return FALSE;
        }
    }
    
    private function save_session($uname, $uid, $email, $last_login, $logged_in = FALSE,$full_name) {
        $save_session = array('uid' => $uid, 'username' => $uname, 'email' => $email, 'last_login' => $last_login, 'logged_in' => $logged_in, 'full_name' => $full_name);
        $this->CI->session->set_userdata($save_session);
    }
    
    private function kill_sessions() {
        $this->CI->session->unset_userdata('uid');
        $this->CI->session->unset_userdata('username');
        $this->CI->session->unset_userdata('email');
        $this->CI->session->unset_userdata('last_login');
        $this->CI->session->unset_userdata('logged_in');
        $this->CI->session->unset_userdata('user_lang');
        $this->CI->session->unset_userdata('full_name');
        session_destroy();
    }
    
    public function proses_logout() {
        $this->kill_sessions();
    }
    
    public function cek_login($email, $pass) {
        $this->CI->db->select('id,username,userpass,email,last_login,first_register,full_name');
        $this->CI->db->where('email', $email);
        $query = $this->CI->db->get('register_user');
        $row = $query->row_array();
        $uid = $row['id'];
        $db_pass = $row['userpass'];
        $c_pass = $this->cek_password($pass, $db_pass);
        if ($c_pass) {
            $this->save_session($row['username'], $uid,  $row['email'], $row['last_login'], TRUE,$row['full_name']);
            $query->free_result();
            $this->CI->db->where('id', $uid);
            $this->CI->db->update('register_user', array('last_login' => date('Y-m-d H:m:s'), 'last_login_ip' => $this->CI->input->ip_address()));
            return TRUE;
        }
        return FALSE;
    }
}
