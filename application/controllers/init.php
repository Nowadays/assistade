<?php
session_start();
if(!isset($_SESSION['result'])){
    $_SESSION['result']="";
}

class Init extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
        $this->load->model('repartition_model');
	}

	public function index(){
        $data['self']='init/';
        $data['title'] = 'Initialisation';  
        $this->load->view('main');
    }
    
    public function createdb(){
        $this->repartition_model->createDatabase();
        redirect('init','refresh');
    }
    
    public function repartition(){
        $result=$this->repartition_model->repartition();
        $_SESSION['result']=$result;
        redirect('init','refresh');
    }
}
?>
