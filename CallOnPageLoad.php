<?php

# THIS IS BASICALLY A BACKEND CALL TO THE MAIN FRAMEWORK/CODEBASE OF THE APPLICATION

# TEMPLATE TO CALL ROUTE ON PAGE LOAD

# STEP 1 CALL THE ROUTE

function CallRouteStage ($STR_RouteKey, $INT_Stage, $ARR_ToPostData = [])
{
    $URL_FRAMEWORK = "http://localhost/DansFramework6/Framework/";

    # SET STAGE TO STAGE 0 BY DEFAULT
    if(!isset($ARR_ToPostData['FRAMEWORK_STAGE']))
    {
        $ARR_ToPostData['FRAMEWORK_STAGE'] = $INT_Stage;
    }

    // use key 'http' even if you send the request to https://...
    # IGNORE ERRORS IS SET TO TRUE, SO WE CAN DEBUG ROUTES, OTHERWISE WHEN BRINGING DEBUG ERRORS IN ROUTES AND DEBUGGING HERE IT WILL JUST CREATE A GENERIC WARNING AND CRASH THE PROGRAM, PLAINTEXT MAKES IT OBVIOUS WHATS GONE WRONG
    $ARR_Options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($ARR_ToPostData),
            'ignore_errors' => true 
        ],
    ];

    $URL_ToCall = $URL_FRAMEWORK . $STR_RouteKey;
    $STREAM_Context = stream_context_create($ARR_Options);
    $STR_Result = file_get_contents($URL_ToCall, false, $STREAM_Context);
    if ($STR_Result === false) 
    {
        //!! FIND A WAY TO DO THIS WITH SOME GRACE
        //!! ERROR
        var_dump($STR_Result);
        die();
    }
    
    $ARR_Response = json_decode($STR_Result, true);

    if (json_last_error() === JSON_ERROR_NONE) 
    {
        # RETURN VALID JSON
        return $ARR_Response;
    }
    else
    {
        //!! FIND A WAY TO DO THIS WITH SOME GRACE
        //!! ERROR
        var_dump($STR_Result);
        die();
    }

}

require 'kint.phar';

# WHAT ROUTE SHALL BE CALLED?
$STR_RouteKey = "guestbookpageload";

$INT_Stage = 0;

# WHAT PARAMETERS SHALL BE PASSED TO THE ROUTE?
$ARR_ToPostData = [];
$ARR_ToPostData['INT_Stage'] = $INT_Stage;
// $ARR_ToPostData['PageLoadPost'] = 'This is an example of seting a post variable';


$ARR_Response = CallRouteStage($STR_RouteKey, $INT_Stage, $ARR_ToPostData);
while($ARR_Response['STR_FrontendCommand'] == 'NEXT_STAGE')
{
    switch ($ARR_Response['STR_FrontendCommand']) {
        case 'NEXT_STAGE':
            # CALL THE NEXT RESPONSE
            $INT_Stage = $ARR_Response['INT_Stage'];
            // $ARR_Response = CallRouteStage($STR_RouteKey, $INT_Stage, $ARR_Response['ARR_Chain']['General']);
            $ARR_Response = CallRouteStage($STR_RouteKey, $INT_Stage, $ARR_Response['ARR_Chain']['General']);
            echo "<br>";
            echo "<br>";
            d($ARR_Response);
            // die();
            break;
        case 'HARD_ERROR':
            # PER PAGE ERROR HANDLING
            break;
    }
}

d($ARR_Response);
