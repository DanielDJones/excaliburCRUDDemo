<?php
session_start();
$ARR_ServerInfo['URL_FrameworkPath'] = 'localhost/ExcaliburCRUD/Framework';
$ARR_ServerInfo['URL_Domain'] = 'localhost/';
$ARR_ServerInfo['URL_PublicPath'] = 'http://localhost/ExcaliburCRUD';

$ARR_ServerInfo['FILEPATH_FrameworkPath'] = realpath(dirname(__FILE__));

# UNPACK GENERAL POST DATA INTO REGULAR POST DATA

function SanatiseVariable($VAR_Value)
{
    $VAR_Value = trim($VAR_Value);
    $VAR_Value = stripslashes($VAR_Value);
    $VAR_Value = htmlspecialchars($VAR_Value);
    return $VAR_Value;
}


if(isset($_POST['General']))
{
    
    # DECODE JSON
    $_POST['General'] = json_decode($_POST['General'], TRUE);
    
    // if($_POST['FRAMEWORK_STAGE'] == 2)
    // {
    //     var_dump($_POST);
    // }

    //!! THIS IS CURRENTLY REQUIRED WHEN GOING FROM PHP, FIND OUT WHY AND REMOVE THE NEED FOR THIS HACK
    if(isset($_POST['General']['General']))
    {
        foreach ($_POST['General']['General'] as $STR_Key => $STR_Value) {
            $_POST[$STR_Key] = $STR_Value;
        }
    }

    if(isset($_POST['General']))
    {
        foreach ($_POST['General'] as $STR_Key => $STR_Value) {
            $_POST[$STR_Key] = $STR_Value;
        }
    }

    
    // # SANATISE THE POST DATA
    // foreach($_POST as $STR_Key => $MIX_Value)
    // {
    //     if(is_array($MIX_Value))
    //     {
    //         foreach($MIX_Value as $STR_Key2 => $MIX_Value2)
    //         {
    //             # REMOVE ARRAYS TO LEVELS DEEP
    //             if(is_array($MIX_Value2))
    //             {
    //                 $_POST[$STR_Key][$STR_Key2] = NULL;
    //             }
                
    //             else
    //             {
    //                 $_POST[$STR_Key][$STR_Key2] = SanatiseVariable($MIX_Value2);
    //             }
    //         }
    //     }
    //     else
    //     {
    //         $_POST[$STR_Key] = SanatiseVariable($MIX_Value);
    //     }
    // }

    // if($_POST['FRAMEWORK_STAGE'] == 2)
    // {
    //     var_dump($_POST);
    // }

}

// $FILEPATH_FrameworkPath = realpath(dirname(__FILE__));

# REGISTER CONTROLLERS HERE
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Controller/Controller.php');
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Controller/GuestBookController.php');

# BRING IN VALIDATION
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Validation.php');

# BRING IN ROUTER AND ROUTES
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/RouteHandler.php');

# BRING IN THE MODELS
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Model/Model.php');
require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Model/GuestBook.php');

$OBJ_Router = new RouteHandler($ARR_ServerInfo);

$ARR_ToJS = $OBJ_Router->CallRouteAtStage();

echo json_encode($ARR_ToJS);


// print_r('Request URI: ');
// print_r($_SERVER['REQUEST_URI']);
// print_r('<br>');

// print_r('Method: ');
// print_r($_SERVER['REQUEST_METHOD']);
// print_r('<br>');

// print('Get Variables: ');
// print_r($_GET);
// print_r('<br>');

// print_r('Post Variables: ');
// print_r($_POST);
// print_r('<br>');

?>