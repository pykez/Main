<?php
    class Users extends Controller{

        public function __construct(){
            $this->userModel = $this->model('User');

        }

        public function register(){
            
            //Check for POST
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // Process form

                //Sanitize POST data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $data = [
                    'name' => trim($_POST['name']),
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),
                    'confirm_password' => trim($_POST['confirm_password']),
                    'name_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err' => ''
                ];

                //Validate Email
                if(empty($data['email'])){
                    $data['email_err'] = 'Por favor, digite o email.';
                } else {
                    //Check if email exists
                    if($this->userModel->findUSerByEmail($data['email'])){
                        $data['email_err'] = 'Email já utilizado.';
                    }
                }

                //validate Name
                if(empty($data['name'])){
                    $data['name_err'] = 'Por favor, digite o nome.';
                }

                //validate Password
                if(empty($data['password'])){
                    $data['password_err'] = 'Por favor, digite a senha.';
                } else if(strlen($data['password']) <6 ) {
                    $data['password_err'] = 'A senha deve conter ao menos 6 caracteres.';
                }

                
                //validate Confirm Password
                if(empty($data['confirm_password'])){
                    $data['confirm_password_err'] = 'Por favor, confirme a senha.';
                } else {
                    if($data['password'] != $data['confirm_password']){
                        $data['confirm_password_err'] = 'As senhas não são iguais.';
                    }
                }

                //Make sure errors are empty
                if(empty($data['email_err']) && empty($data['name_err']) && 
                empty($data['password_err']) &&  empty($data['confirm_password_err'])){
                    
                    //Input data on database

                    //Hash password
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                    //Register User
                   if($this->userModel->register($data)){

                    flash('register_success', 'Você está registrado e pode fazer o log in.');

                    redirect('users/login');

                   } else {
                       die ('something went wrong');
                   }


                } else {

                    //Load view with errors
                    $this->view('users/register', $data);
                }


            } else {
                // Init data

                $data = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'confirm_password' => '',
                    'name_err' => '',
                    'email_err' => '',
                    'password_err' => '',
                    'confirm_password_err' => ''
                ];

                // Load View
                $this->view('users/register', $data);

            }
        }


        public function login(){
            
            //Check for POST
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // Process form

                //Sanitize POST data
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $data = [
                    'email' => trim($_POST['email']),
                    'password' => trim($_POST['password']),

                    'email_err' => '',
                    'password_err' => '',
                ];

                //Validate Email
                if(empty($data['email'])){
                    $data['email_err'] = 'Por favor, digite o e-mail';
                } 

                //Check for user email

                if($this->userModel->findUserByEmail($data['email'])){
                    // User found
                   
                    // Check and set logged in user
                    // OBS: login method only returns true or false
                    $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                    if($loggedInUser){
                        //Create Session
                        
                        $this->createUserSession($loggedInUser);

                        
                    } else {
                        $data['password_err'] = 'Senha incorreta.';
                        $this->view('users/login', $data);
                    }

                } else {
                        //No user found
                        if (empty($data['email_err'])){
                        $data['email_err'] = 'Usuário não foi encontrado.';
                    }
                }

                //validate Password
                if(empty($data['password'])){
                    $data['password_err'] = 'Por favor digite a senha.';
                } else if(strlen($data['password']) <6 ) {
                    $data['password_err'] = 'A senha deve conter pelo menos 6 caracteres.';
                }

                //Make sure errors are empty
                if(empty($data['email_err']) && 
                empty($data['password_err'])){
                    die('success');
                } else {

                    //Load view with errors
                    $this->view('users/login', $data);
                }
                


            } else {
                // Init data

                $data = [
                    'email' => '',
                    'password' => '',

                    'email_err' => '',
                    'password_err' => '',
                ];

                // Load View
                $this->view('users/login', $data);

            }
        }

        public function createUserSession($user){

            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;

            redirect('posts/index');
 
        }

        public function logout(){
            // Load View
            $this->view('users/wait');
            unset($_SESSION['user_id']);
            unset($_SESSION['user_name']);
            unset($_SESSION['user_email']);
            session_destroy();
            echo('"<script type="text/javascript" src="'.URLROOT.'/js/facebook_login/facebook_logout.js"></script>"');
        }

        public function loginfb(){
        
            //Find user by email
            if ($this->userModel->findUserByEmail($_POST['email'])) {
                //Just log in
                // Check and set logged in user
                // OBS: login method only returns true or false
                $loggedInUser = $this->userModel->loginfb($_POST['email']);
                //Create Session
                $this->createUserSession($loggedInUser);
            } else {
                //Needs to register and log in
                //Hash password
                $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                //register and log in
                $this->userModel->register($_POST);
                $loggedInUser = $this->userModel->loginfb($_POST['email']);
                $this->createUserSession($loggedInUser);
            }

        
        }



    }