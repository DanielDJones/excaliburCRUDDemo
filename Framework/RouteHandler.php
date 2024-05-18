<?php 

/*

THIS CLASS HANDLES BOUTH THE ROUTING AND THE CONTROLLER, 
THIS IS ALSO RESPONSIBLE FOR HANLDEING EACH PART OF THE PROCESS 
ALLOWING FOR MIXING AND MATCHING JS, PHP AND 3rd PARTY FUNCTIONS

*/



class RouteHandler
{
    public $STR_RequestURI;
    public $STR_RequestMethod;
    public $UNIXTIME_RouteStarted;
    public $ARR_Routes;
    public $STR_RouteKey;
    private $ARR_ServerInfo;
    public $INT_Stage = 0;
    # //?? ARR_RouteWarnings STORES WARNINGS TO HELP WITH DEBUGGING
    public $ARR_RouteWarnings = [];

    # CHAIN INFO, USED TO PASS DATA BETWEEN STAGES
    public $ARR_Chain = [];

    function __construct($ARR_ServerInfo)
    {
        require_once($ARR_ServerInfo['FILEPATH_FrameworkPath'] . '/Routes.php');
        $this->ARR_Routes = $ARR_Routes;

        # BASIC CONSTRUCTOR
        $this->STR_RequestURI = $_SERVER['REQUEST_URI'];
        $this->STR_RequestMethod = $_SERVER['REQUEST_METHOD'];
        $this->ARR_ServerInfo = $ARR_ServerInfo;
        $this->STR_RouteKey = $this->GetRouteKey();

        # THIS WILL BE SET SHOULD THE ROUTE HANDLER DETERMINE THE ROUTE IS STARTING
        $this->UNIXTIME_RouteStarted = NULL;

    }

    /*
        GETS THE ROUTE KEYS FROMT THE URL, THIS WILL THEN BE USED LATER TO DETERMIN THE ROUTE AND WHAT CODE TO EXECUTE IN WHAT ORDER
    */
    public function GetRouteKey()
    {
        # NO ROUTE REQUESTED GUARD CLAUSE
        if($this->STR_RequestURI == NULL) { return NULL; }
        
        # GET ROUTE KEY
        $ARR_CompleteKey = [];

        $ARR_URIParts = explode('/', $this->STR_RequestURI);

        $BOOL_FoundFrameworkKeyword = FALSE;
        foreach ($ARR_URIParts as $STR_URIPart) {

            # REMOVE NULLS FROM KEYS TO PREVENT // IN URL
            if($STR_URIPart == NULL) { continue; }

            # FOUND KEYWORD SO THE FRAMEWORK CAN NOW START BUILDING THE ROUTE
            if($STR_URIPart === 'Framework')
            {
                $BOOL_FoundFrameworkKeyword = TRUE;
                continue;
            }

            # SKIP OVER ADDING THE PARTS OF THE ROUTE KEY IF THE KEYWORD HAS NOT BEEN FOUND
            if(!$BOOL_FoundFrameworkKeyword) { continue; }

            # ADD PARTS OF THE ROUTE KEY TO THE ARRAY
            $ARR_CompleteKey[] = $STR_URIPart;
        }

        # RETURN THE ROUTE KEY
        return implode('/', $ARR_CompleteKey);

    }

    private function ClearRouteSession()
    {
        # CLEAR THE SESSION
        $_SESSION['FRAMEWORK_ROUTE'] = NULL;
        return;
    }

    private function SetRouteSession()
    {
        # SET THE SESSION
        $_SESSION['FRAMEWORK_ROUTE']['RouteKey'] = $this->STR_RouteKey;
        $_SESSION['FRAMEWORK_ROUTE']['RequestMethod'] = $this->STR_RequestMethod;
        $_SESSION['FRAMEWORK_ROUTE']['RouteStarted'] = $this->UNIXTIME_RouteStarted;
        $_SESSION['FRAMEWORK_ROUTE']['InitialInput'] = $_POST;
        return;
    }

    public function CallRouteAtStage()
    {
        # GET STAGE FROM REQUEST IF POST, GET REQUEST WILL ONLY BE USED FOR PAGES OR 1 ACTION ROUTES SUCH AS VIEW A LISITNG OR PRODUCT
        if($this->STR_RequestMethod == 'GET')
        {
            $this->INT_Stage = 0;
        }
        else if (isset($_POST['FRAMEWORK_STAGE']))
        {

            //!! ADD SAFETEY CHECKS HERE TO ENSURE THE STAGE IS THE CORRECT ONE
            $this->INT_Stage = $_POST['FRAMEWORK_STAGE'];
            
        }
        else
        {
            $this->INT_Stage = 0;
        }

        if($this->INT_Stage == 0)
        {
            # SET THE START TIME OF THE ROUTE
            $this->UNIXTIME_RouteStarted = time();

            # REMOVE OLD ROUTE SESSION AND SET THE NEW ONE
            $this->ClearRouteSession();
            $this->SetRouteSession();
        }


        # GUARD CLUASES FOR NO DEFINED ROUTE
        if(!isset($this->ARR_Routes[$this->STR_RequestMethod])) { $this->ARR_RouteWarnings[] = 'No routes found for this request method'; return; }
        if(!isset($this->ARR_Routes[$this->STR_RequestMethod][$this->STR_RouteKey])) { $this->ARR_RouteWarnings[] = 'No routes found for this request'; return;}
        if(!isset($this->ARR_Routes[$this->STR_RequestMethod][$this->STR_RouteKey][$this->INT_Stage])) { $this->ARR_RouteWarnings[] = 'No routes found for this request'; return; }


        # GET WHERE THE STAGE SHOULD BE HANDLED FORM THE BACKEND
        $ARR_RouteAtStageInfo = $this->ARR_Routes[$this->STR_RequestMethod][$this->STR_RouteKey][$this->INT_Stage];

        $BOOL_NextStageExists = isset($this->ARR_Routes[$this->STR_RequestMethod][$this->STR_RouteKey][$this->INT_Stage + 1]);

        # RUN VALIDATION HERE
        $OBJ_RouteValidation = new Validation();
        $OBJ_RouteValidation->RouteData($ARR_RouteAtStageInfo[2]);

        # IF VALIDATION FAILS RETURN A VALIDATION FAILED COMMAND TO THE FRONTEND, THAT CAN BE USED TO SHOW IT TO THE USER ON THE FORM
        if(!$OBJ_RouteValidation->BOOL_Valid) { 
            $this->ARR_RouteWarnings[] = 'Validation Failed'; 
            $ARR_ToJS['STR_FrontendCommand'] = 'VALIDATION_FAILED';
            $ARR_ToJS['ARR_FieldsNotValid'] = $OBJ_RouteValidation->ARR_VariablesNotValid;
    
            return $ARR_ToJS; 
        }

        # GUARD CLAUSE IF STAGE ISNT FOUND
        if(!isset($ARR_RouteAtStageInfo)) { $this->ARR_RouteWarnings[] = 'No routes found for this request'; return; }

        if(in_array($ARR_RouteAtStageInfo[0], ['VIEW', 'View', 'view']))
        {
            # GET VIEW NAME
            $STR_ViewName = $ARR_RouteAtStageInfo[1];
        }
        elseif(in_array($ARR_RouteAtStageInfo[0], ['REDIRECT', 'Redirect', 'redirect']))
        {
            # REDIRECT TO ANOTHER ROUTE
            $STR_RedirectTo = $ARR_RouteAtStageInfo[1];
        }
        else
        {
            # GET THE CONTROLLER AND METHOD
            $ARR_ControllerAndMethod = explode('@', $ARR_RouteAtStageInfo[1]);
        
            # GUARD CLAUSE FOR INCORRECT ROUTE FORMAT
            if(count($ARR_ControllerAndMethod) != 2) { $this->ARR_RouteWarnings[] = 'Route is not in the correct format, expecting Controller@Method'; return; }

            # GET INFO TO EITHER SEND TO JS OR TO INVOKE THE CONTROLLER
            $STR_Controller = $ARR_ControllerAndMethod[0];
            $STR_Method = $ARR_ControllerAndMethod[1];
        }

        # THE FIRST PART OF THE ROUTE INFO DETERMINS WHERE WE ARE GOING TO HANDLE THE REQUEST
        switch($ARR_RouteAtStageInfo[0])
        {
            case 'PHP':
            case 'php':
                
                # CALL THE CONTROLLER
                $ARR_ControllerResponse = $this->CallController($STR_Controller, $STR_Method);
                

                # CONTROLLER RESPONSE
                /*
                    ? KEYS: INT_STATUS, STR_MSG, ARR_CHAIN
                    ? STATUS, 0: TECHNICAL ERROR, 1: SUCCESS, 2: FORM ERROR
                */
                switch($ARR_ControllerResponse['INT_STATUS'])
                {
                    case 0:
                        # TECHNICAL ERROR
                        $ARR_ToJS = [];
                        $ARR_ToJS['STR_FrontendCommand'] = 'HARD_ERROR';
                        break;

                    case 1:
                        # SUCCESS, RUN THE NEXT STAGE(AS THIS IS NOT A REDIRECT)
                        $ARR_ToJS = [];

                        # PREVENT THE FRAMEWORK FROM RUNNING OVER THE END OF ROUTE
                        if($BOOL_NextStageExists)
                        {
                            $ARR_ToJS['STR_FrontendCommand'] = 'NEXT_STAGE';
                        }
                        else
                        {
                            $ARR_ToJS['STR_FrontendCommand'] = 'END_ROUTE';
                        }

                        break;

                    case 2:
                        # FORM ERROR
                        $ARR_ToJS['VALIDATION_FAILED'] = 'FORM_ERROR';
                        
                        break;
                }

                $ARR_ToJS['STR_Msg'] = $ARR_ControllerResponse['STR_MSG'];
                $ARR_ToJS['ARR_Chain'] = $ARR_ControllerResponse['ARR_Chain'];
                $ARR_ToJS['INT_Stage'] = $this->INT_Stage + 1;
                $ARR_ToJS['STR_RouteKey'] = $this->STR_RouteKey;

                # UPDATE THE CHAIN
                $this->ARR_Chain = $ARR_ControllerResponse['ARR_Chain'];

                break;

            case 'JS':
            case 'js':
                # CALL THE JS

                $ARR_ToJS['STR_FrontendCommand'] = 'RUN_FrontendController';
                $ARR_ToJS['STR_FrontendController'] = $STR_Controller;
                $ARR_ToJS['STR_FrontendMethod'] = $STR_Method;
                $ARR_ToJS['ARR_Chain'] = json_encode($this->ARR_Chain);
                $ARR_ToJS['ARR_RoutePost'] = json_encode($_POST);

                //?? WE DONT ADD A 1 TO STAGE AS THE JS WILL DO THIS ONCE THE FUNCTION IS COMPLETE
                $ARR_ToJS['INT_Stage'] = $this->INT_Stage;

                break;

            case 'VIEW':
            case 'View':
            case 'view':

                # CALL THE VIEW, THE FINAL STEP IN THE ROUTE AND A PAGE REDIRECT OR REFRESH
                $_SESSION['VIEW_ROUTE'] = [];
                $_SESSION['VIEW_ROUTE']['STR_Name'] = $STR_ViewName; 
                $_SESSION['VIEW_ROUTE']['ARR_Chain'] = $_POST;

                $ARR_ToJS = [];
                $ARR_ToJS['STR_FrontendCommand'] = 'REDIRECT';
                $ARR_ToJS['STR_ViewName'] = $STR_ViewName;
                $ARR_ToJS['STR_ViewURL'] = $this->ARR_ServerInfo['URL_PublicPath'] . '/' . $STR_ViewName . '.php';
                
                break;

            case 'REDIRECT':
            case 'Redirect':
            case 'redirect':
                
                # REDIRECT TO ANOTHER ROUTE
                $ARR_ToJS = $this->RedirectToRoute($STR_RedirectTo, $_POST);

                return $ARR_ToJS;
                break;

            default:
                $this->ARR_RouteWarnings[] = 'Route is not in the correct format, expecting either PHP or JS';
                break;
        }

        return $ARR_ToJS;
    }

    # REDIRECT TO ROUTE FROM ANOTHER, OPTIONAL INPUT SIMULATES A POST REQUEST
    private function RedirectToRoute($STR_RouteKey, $ARR_RouteInput = [])
    {
        $ARR_RouteInput['FRAMEWORK_REFERRERROUTE'] = $this->STR_RouteKey;
        $ARR_ToJS = [];
        $ARR_ToJS['STR_Msg'] = 'SPAWNING EXTRA ROUTE';
        $ARR_ToJS['ARR_Chain'] = $ARR_RouteInput;
        $ARR_ToJS['INT_Stage'] = 0;
        $ARR_ToJS['STR_RouteKey'] = $STR_RouteKey;
        $ARR_ToJS['FRAMEWORK_REFERRERROUTE'] = $this->STR_RouteKey;

        //!! TESTING ONLY!
        // http_response_code(400);
        // var_dump($ARR_ToJS);
        // die();

        $ARR_ToJS['STR_FrontendCommand'] = 'SPAWN_ROUTE';

        return $ARR_ToJS;
    }

    private function CallController($STR_ControllerName, $STR_MethodName)
    {
        
        # GUARD CLUASE FOR UNDEFINED CONTROLLER
        if(!class_exists($STR_ControllerName))
        {
            $this->ARR_RouteWarnings[] = 'Class does not exist {' . $STR_ControllerName . '}';
            return;
        }

        # CREATE THE CONTROLLER OBJECT
        $OBJ_Controller = new $STR_ControllerName();
        
        # GUARD CLAUSE FOR UNDEFINED METHOD
        if(!method_exists($OBJ_Controller, $STR_MethodName)) 
        { 
            $this->ARR_RouteWarnings[] = 'Method does not exist {' . $STR_MethodName . '} from class {' . $STR_ControllerName . '}'; 
            return; 
        }

        # CALL FUNCTION
        $ARR_ControllerResponse = $OBJ_Controller->$STR_MethodName($this->ARR_Chain);

        # THERE IS A TECHNICAL ERROR IF THERE IS NO STATUS
        if(!isset($ARR_ControllerResponse['INT_STATUS'])) { $this->ARR_RouteWarnings[] = 'Controller Response is missing STATUS'; $ARR_ControllerResponse['STATUS'] = 0; }

        # RETURN WITH RESPONSE
        return $ARR_ControllerResponse;
    }

}

?>