<?php
    class Home extends Controller{


        public function __construct(){
            //echo 'Pages loaded';

       
        }

        public function index(){

        
            $data = [
                'title' => '',
            ];

            $this->view('home/index', $data);
        
        }

        public function about(){

            $data = [
                'title' => 'About us',
            ];

            $this->view('home/about', $data);

            
        }

    }