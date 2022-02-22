<?php

App::uses('AppModel', 'Model');

class UserProfile extends AppModel {

    public $validate = [
        'nombre' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'descripcion' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'permisos' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'perfil' => [
            'notBlank' => [
                'rule' => ['notBlank', 'unique'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
        'habilitado' => [
            'boolean' => [
                'rule' => ['boolean'],
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ],
        ],
    ];

    public function getList() {
        return $this->find('list', ['conditions' => ['UserProfile.habilitado' => 1], 'fields' => ['UserProfile.id', 'UserProfile.nombre']]);
    }

    public function isUnique($perfil, $or = true) {// dejar el $or porq estoy redefiniendo isUnique
        return empty($this->find('list', ['conditions' => ['perfil' => $perfil]]));
    }

    public function guardar($data) {
        if (!isset($data['UserProfile']['id']) && isset($data['UserProfile']['perfil']) && !empty($data['UserProfile']['perfil']) && !$this->isUnique($data['UserProfile']['perfil'])) {
            // ya existe un perfil creado para este perfil
            return ['e' => 1, 'd' => 'Existe un perfil ya creado para el perfil seleccionado'];
        }

        if (!isset($data['UserProfile']['permisos'])) {
            return ['e' => 1, 'd' => 'No seleccionó ningun permiso'];
        }
        $controlleractions = $this->getAllControllerActions();
        $permisos = ["noticias/index", "users/tyc", "users/login", "users/logout"]; // x defecto se agregan estos
        foreach ($data['UserProfile']['permisos'] as $v) {
            if (isset($controlleractions[$v])) {
                $permisos[] = strtolower($controlleractions[$v]);
            }
        }
        if (!isset($data['UserProfile']['id'])) {// si es add, hago create
            $this->create();
        }
        $data['UserProfile']['permisos'] = json_encode(array_values(array_unique($permisos))); //quito duplicados antes de guardar
        if ($this->save($data['UserProfile'])) {
            return ['e' => 0];
        }
        return ['e' => 1, 'd' => 'No seleccionó ningun permiso'];
    }

    /*
     * Obtiene los permisos del perfil
     */

    public function getPermisos($perfil) {
        $resul = $this->find('list', ['conditions' => ['perfil' => $perfil], 'fields' => ['id', 'permisos'], 'limit' => 1]);
        if (empty($resul)) {
            return [];
        }
        try {
            return json_decode(reset($resul));
        } catch (Exception $ex) {
            return [];
        }
    }

    /*
     * Obtiene todas los controller/actions del sistema para poder seleccionar los permisos de cada perfil
     */

    public function getAllControllerActions() {
        $dir = APP . "Controller" . DS;
        $resul = [];
        if (is_dir($dir)) {
            $excluir = ['AppController.php', 'MobAppController.php', 'WebrootController.php'];
            $objects = scandir($dir);
            foreach ($objects as $object) {// para todos los archivos menos $excluir
                if (filetype($dir . "/" . $object) === "file" && !in_array($object, $excluir)) {
                    try {
                        $handle = fopen($dir . "/" . $object, "r");
                        while (($line = fgets($handle)) !== false) {
                            $pos = strpos($line, "function");

                            // no tomo en cuenta si es beforeFilter, private function o panel_
                            if ($pos !== false && strpos($line, "beforeFilter") === false && substr($line, $pos + 9, 1) !== "_" && strpos($line, "panel_") === false) {
                                $parentesis = strpos($line, "(");
                                //echo substr($object, 0, -14) . "/" . substr($line, $pos + 9, $parentesis - $pos - 9) . "<br>";
                                $resul[] = strtolower(substr($object, 0, -14) . "/" . substr($line, $pos + 9, $parentesis - $pos - 9));
                            }
                        }
                        fclose($handle);
                        $resul[] = strtolower(substr($object, 0, -14) . "/editar");
                        $resul[] = strtolower(substr($object, 0, -14) . "/invertir");
                    } catch (Exception $e) {
                        
                    }
                }
            }
            reset($objects);
        }
        return $resul;
    }

    // funcion de busqueda
    public function filterName($data) {
        $data['buscar'] = filter_var($data['buscar'], FILTER_SANITIZE_STRING);
        if (empty($data['buscar'])) {
            return [];
        }
        return [
            'OR' => [
                'UserProfile.nombre LIKE' => '%' . $data['buscar'] . '%',
        ]];
    }

}
