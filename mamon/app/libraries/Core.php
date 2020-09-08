<?php

    /*
    * App Core Class
    * creates URL & loads core controller
    * URL FORMAT - /controller/method/params
    */

    class Core {
        protected $currentController = 'Home';
        protected $currentMethod = 'index';
        protected $params = [];

        public function __construct(){
            //print_r($this->getUrl());
            
            $url = $this->getUrl();

            /* Look in controllers for first value
            ucwords uppercase the first character
            because controllers are classes
            and classes starts with uppercase.
            */
            if(file_exists('../app/controllers/' .ucwords($url[0]).'.php')){
                // If exists, set as controller
                $this->currentController = ucwords($url[0]);
                // Unset 0 Index
                unset($url[0]);
            }

            //Require the controller
            require_once '../app/controllers/' . $this->currentController . '.php';

            //Instantiate
            $this->currentController = new $this->currentController;

            //Check for second part of url
            if (isset($url[1])){
                //Check to see if method exists on current controller
                if(method_exists($this->currentController, $url[1])){
                    $this->currentMethod = $url[1];
                }
                unset($url[1]);
            }

            //echo $this->currentMethod;

            //Get params
            $this->params = $url ? array_values($url) : [];
            //print_r( $this->params);

            /* Call a callback with array of params
             Chama as funções de usuário e passa o argumento
            Ou seja, o resto da $url virou parametros
            Obs, não se chama métodos de classe instanciadas
            dentro de outra classe da forma normal,
            $this->currenntController->$this->currentMethod
            por isso os brackets ali.
            */\

            call_user_func_array([$this->currentController, 
            $this->currentMethod], $this->params);



        }

        //This function will break the url in parts.
        public function getUrl(){
            if(isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                return $url;
            }
        }


    }

    