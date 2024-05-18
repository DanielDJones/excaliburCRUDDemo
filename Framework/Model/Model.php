<?php

class Model
{
    public $OBJ_PDO;

    function __construct()
    {
        # SETUP DB CONNECTION
        $STR_Host = 'localhost';
        $STR_User = 'root';
        $STR_Pass = '';
        $STR_DBName = 'DansFramework';
        $STR_DSN = 'mysql:host=' . $STR_Host . ';dbname=' . $STR_DBName;

        # INIT PDO
        $this->OBJ_PDO = new PDO($STR_DSN, $STR_User, $STR_Pass);

        # SET ATTRIBUTES OF PDO
        $this->OBJ_PDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->OBJ_PDO->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}

?>