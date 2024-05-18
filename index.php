<?php


function CallRouteStage ($STR_RouteKey, $INT_Stage, $ARR_ToPostData = [])
{
    $URL_FRAMEWORK = "http://localhost/ExcaliburCRUD/Framework/";

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

//?? THIS WILL LOOP THROUGH EACH BLOCK, HOWEVER IF ONLY SUPPORTS THE NEXT BLOCK COMMAND AND HARD ERRORS. IT IS POSSIBLE TO CALL A ROUTE ON PAGELOAD WITH JS IF YOU NEED THESE FEATURES AND EXPECT THE CLIENT TO HAVE JS ENABLED(AND DONT NEED TO SLIGHT DELAY ON LOAD)
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
            // die();
            break;
        case 'HARD_ERROR':
            # PER PAGE ERROR HANDLING
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excalibur CRUD Demo</title>
    <!-- <link rel="stylesheet" href="CSS/Main.css"> -->
    <link rel="stylesheet" href="CSS/output.css">
</head>
<body>

    <div class="flex flex-col bg-gray-600 h-dvh overflow-hidden">
        <!-- TOP NAVBAR -->
        <nav class="z-10 h-32 bg-blue-950 flex border-yellow-600 border-b-8">
            <div class="w-full flex flex-row justify-start mt-auto mb-auto pl-16">
                <span class="text-5xl font-bold text-gray-50">Excalibur CRUD</span>
            </div>
            <ul class="w-full flex flex-row justify-end mt-auto mb-auto pr-16 gap-8">
                <li class="text-2xl font-bold text-gray-50">Home</li>
                <li class="text-2xl font-bold text-gray-50">Excalibur Framework</li>
                <li class="text-2xl font-bold text-gray-50">View Code</li>
            </ul>
        </nav>

        <div class="flex flex-row h-full">
            
            <aside class="h-full w-1/3 max-w-lg bg-blue-950 flex flex-col content-center border-yellow-600 border-r-8 p-8">
            
                <form id="addpost" class=" w-full max-w-64 mx-auto">
                    <h2 class="text-2xl font-bold text-gray-50">Add New</h2>
                    <div class="flex flex-col gap-2 my-8">
                        <label for="name" class="text-gray-50">Name</label>
                        <input type="text" name="name" id="name" class="bg-gray-50 p-2">
                    </div>
                    <div class="flex flex-col gap-2 my-8">
                        <label for="msg" class="text-gray-50">Name</label>
                        <textarea name="msg" id="msg" class="bg-gray-50 p-2"></textarea>
                    </div>
                    <button onclick="StartRoute('guestbookadd', 'addpost')" type="button" class="bg-yellow-600 text-gray-50 h-8 w-32">
                        Add
                    </button>
                </form>

                <form id="editpost" class=" w-full max-w-64 mx-auto my-16 hidden">
                    <h2 class="text-2xl font-bold text-gray-50">Add New</h2>
                    <input type="text" id="edit-id">
                    <div class="flex flex-col gap-2 my-8">
                        <label for="name" class="text-gray-50">Name</label>
                        <input type="text" name="name" id="edit-name" class="bg-gray-50 p-2">
                    </div>
                    <div class="flex flex-col gap-2 my-8">
                        <label for="msg" class="text-gray-50">Name</label>
                        <textarea name="msg" id="edit-msg" class="bg-gray-50 p-2"></textarea>
                    </div>
                    <button onclick="StartRoute('guestbookedit', 'editpost')" type="button" class="bg-yellow-600 text-gray-50 h-8 w-32">
                        Add
                    </button>
                </form>
            </aside>


            
            <main id="guestbookentries" class="w-max flex flex-col m-8 gap-16 p-16 max-w-screen-lg mx-auto overflow-y-scroll">
                <?= $ARR_Response['ARR_Chain']['htmlrows'] ?>
                
            </main>

        </div>
    </div>


</body>
</html>

<!-- VALIDATION -->


<!-- CONTROLLERS -->
<script src="ClientSide/Controller/GuestBookController.js"></script>

<!-- MAIN FRAMEWORK --> 
<script src="Framework.js"></script>
