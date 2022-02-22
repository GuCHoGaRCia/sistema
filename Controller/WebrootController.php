<?php

App::uses('AppController', 'Controller');

class WebrootController extends AppController {

    public function index() {
        header("Location: https://ceonline.com.ar/sistema/Users/login");
    }

}
