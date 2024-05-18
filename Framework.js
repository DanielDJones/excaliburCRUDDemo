URL_Framework = "http://localhost/ExcaliburCRUD/Framework/";

var OBJ_Instruction = {};
var OBJ_RoutesReadyForNextBlock = {};

// CALL / START A ROUTE
async function StartRoute(STR_RouteKey, STR_FormID = undefined)
{

    // CLEAR ANY ERRORS ON THE FORM
    await ClearUIErrors();
    var URL_Route = URL_Framework + STR_RouteKey; 

    if(STR_FormID != undefined)
    {
        var NODE_TragetForm = document.getElementById(STR_FormID);

        // GUARD CLUASE, FORM SPECIFIED BUT NOT FOUND
        if(NODE_TragetForm == null)
        {
            console.error("FORM NOT FOUND ", STR_FormID);
            return;
        }

        var OBJ_FormData = new FormData(NODE_TragetForm);
        console.log("OBJ_FormData ", OBJ_FormData);


    }
    else
    {
        var OBJ_FormData = new FormData();
    }

    // SETUP ROUTE
    if(OBJ_Instruction[URL_Route] == null)
    {
        OBJ_Instruction[URL_Route] = {};
    }
    OBJ_Instruction[URL_Route]['POST'] = OBJ_FormData;
    OBJ_Instruction[URL_Route]['POST']['FRAMEWORK_STAGE'] = 0;

    // SET ROUTE AS READY FOR NEXT BLOCK, STARTING THE ROUTE
    OBJ_RoutesReadyForNextBlock[URL_Route] = true;

}

async function CheckForNextBlock() {
    
    for(const STR_Route in OBJ_RoutesReadyForNextBlock) 
    {
        if(OBJ_RoutesReadyForNextBlock[STR_Route]) 
        {
            OBJ_RoutesReadyForNextBlock[STR_Route] = false;
            console.log("NEXT BLOCK INSTRUCTION ", OBJ_Instruction[STR_Route]);
            // OBJ_RoutesReadyForNextBlock[STR_Route] = true;
            await NextBlock(STR_Route, OBJ_Instruction[STR_Route]['POST'], OBJ_Instruction[STR_Route]['STAGE']);
        }
    }
}

async function NextBlock(URL_Route, OBJ_PostData, INT_Stage) {
    fetch(URL_Route, 
    {
        method: "POST",
        body: OBJ_PostData,
    }
    )
    .then((OBJ_Response) => 
    {
        //!! FIND PROPER WAY TO HANDLE ERROR
        if (!OBJ_Response.ok) {

            throw new Error("network returns error");
        }
        console.error("OBJ_Response ", OBJ_Response);

        return OBJ_Response.json();
        
    }
    )
    .then((OBJ_Response) => 
    {
        HandleBackendResponse(OBJ_Response, URL_Route);
    }
    )
    .catch((error) => {
        // Handle error
        console.log("error ", error);
    }
    );
}

// CLEAR ERRORS FROM FORM
async function ClearUIErrors() {
    var OBJ_InputsOnForm = document.querySelectorAll("input");
    for (INT_A = 0; INT_A < OBJ_InputsOnForm.length; ++INT_A) {
        // deal with inputs[index] element.
        OBJ_InputsOnForm[INT_A].classList.remove("InputError");
    }
}

async function HandleBackendResponse(OBJ_Response, URL_Route) {
    console.log("OBJ_Response ", OBJ_Response);
    // console.log("URL_Route ", URL_Route);
    // console.log("OBJ_ARR_Chain ", OBJ_Response.ARR_Chain);
    switch (OBJ_Response.STR_FrontendCommand) {
        case "NEXT_STAGE":
            // HANDLE SUCCESS
            
            FORMDATA_PostData = new FormData();
            // for(const [key, value] of OBJ_Response.ARR_Chain.entries())
            // {
            //     FORMDATA_PostData.append(key, value);
            // }

            FORMDATA_PostData.append("General", JSON.stringify(OBJ_Response.ARR_Chain));
            FORMDATA_PostData.append("FRAMEWORK_STAGE", OBJ_Response.INT_Stage);

            OBJ_Instruction[URL_Route]['POST'] = FORMDATA_PostData;
            OBJ_Instruction[URL_Route]['POST']['FRAMEWORK_STAGE'] = OBJ_Response.INT_Stage;
            
            // SET ROUTE AS READY FOR NEXT BLOCK
            OBJ_RoutesReadyForNextBlock[URL_Route] = true;

            break;
        
        case "RUN_FrontendController":
            STR_Controller = OBJ_Response.STR_FrontendController;
            STR_Function = OBJ_Response.STR_FrontendMethod;

            // INITIALIZE CONTROLLER
            CLASSDEF_Controller = window[STR_Controller];
            OBJ_Controller = new CLASSDEF_Controller();

            // VALIDATE


            JSON_PostData = OBJ_Response.ARR_RoutePost;
            OBJ_PostData = JSON.parse(JSON_PostData);

            // RUN FUNCTION
            var OBJ_FunctionResults = await OBJ_Controller[STR_Function](OBJ_PostData);
            switch(OBJ_FunctionResults.INT_STATUS)
            {
                case 0:
                    // HANDLE TECHNICAL ERROR
                    break;

                case 1:
                    
                    //!! REPLACE THIS CODE WITH AN ACKNOWLEDGEMENT CHECK ON THE BACKEND THAT WILL TELL THE FRAMEWORK WHAT TO DO NEXT
                    console.warn("OBJ_FunctionResults SUCCESS ", OBJ_FunctionResults);
                    console.error("OBJ CHAIN ", OBJ_Response);
                    
                    var INT_NextStage = JSON.parse(OBJ_Response.INT_Stage) + 1;
                    FORMDATA_PostData = new FormData();

                    FORMDATA_PostData.append("General", JSON.stringify(OBJ_FunctionResults.OBJ_ChainedVars));
                    FORMDATA_PostData.append("FRAMEWORK_STAGE", INT_NextStage);

                    OBJ_Instruction[URL_Route]['POST'] = FORMDATA_PostData;
                    OBJ_Instruction[URL_Route]['POST']['FRAMEWORK_STAGE'] = INT_NextStage;

                    // SET ROUTE AS READY FOR NEXT BLOCK
                    OBJ_RoutesReadyForNextBlock[URL_Route] = true;

                    // HANDLE SUCCESS
                    break;

                case 2:
                    // HANDLE FORM ERROR
                    break;

            }
            break;
        case "VALIDATION_FAILED":
            // 1. CLEAR ALL ERRORS ON THE FORM
            // 2. DISPLAY ERRORS ON THE FORM

            ARR_FieldsNotValid = OBJ_Response.ARR_FieldsNotValid;
            console.log("OBJ_FieldsNotValid ", ARR_FieldsNotValid);
            var OBJ_InputsOnForm = document.querySelectorAll("input");
            for (INT_A = 0; INT_A < OBJ_InputsOnForm.length; ++INT_A) {
                // deal with inputs[index] element.
                OBJ_InputsOnForm[INT_A].classList.remove("InputError");

                // IF THERE IS AN ERROR FOR THIS INPUT THEN DISPLAY IT
                console.log("OBJ_InputsOnForm[INT_A].name ", OBJ_InputsOnForm[INT_A].name);

                if (ARR_FieldsNotValid.includes(OBJ_InputsOnForm[INT_A].name)) {
                    OBJ_InputsOnForm[INT_A].classList.add("InputError");
                }
            }

            break;
        case "error":
            // HANDLE ERROR
            break;

        case "REDIRECT":
            // HANDLE REDIRECT, SUCH AS SHOWING A VIEW
            let URL_Location = OBJ_Response.STR_ViewURL;
            window.location.href = URL_Location;
            // location.replace('dbtest.php');
            break;
        
        case "SPAWN_ROUTE":
            if(OBJ_Response.FRAMEWORK_REFERRERROUTE == 'error')
            {
                console.error("DAN TOLD THE FRAMEWORK TO STOP HERE DUE TO INFINATE LOOPS ", OBJ_Response);
                break;
            }
            // HANDLE SPAWNROUTE
            await ClearUIErrors();
            // var NODE_FORM_testform = document.getElementById(STR_FormID);
        
            // var OBJ_FormData = new FormData(NODE_FORM_testform);
            console.log(OBJ_Response);
            // OBJ_FormData.append("FRAMEWORK_STAGE", "1");
        
            // console.log("OBJ_FormData ", OBJ_FormData);
            
            var URL_Route = "http://localhost/DansFramework6/Framework/" + OBJ_Response.STR_RouteKey;
        
            if(OBJ_Instruction[URL_Route] == null)
            {
                OBJ_Instruction[URL_Route] = {};
            }

            FORMDATA_PostData = new FormData();
            FORMDATA_PostData.append("General", JSON.stringify(OBJ_Response.ARR_Chain));
            FORMDATA_PostData.append("FRAMEWORK_STAGE", 0);

            OBJ_Instruction[URL_Route]['POST'] = FORMDATA_PostData;
            OBJ_Instruction[URL_Route]['POST']['FRAMEWORK_STAGE'] = 0;
        
            // // SET ROUTE AS READY FOR NEXT BLOCK
            OBJ_RoutesReadyForNextBlock[URL_Route] = true;
            break;

        default:
            if(OBJ_Response.status != undefined)
            {
                console.error("unknown response status ", OBJ_Response.status);
                console.error("Full Body ", OBJ_Response);
            }
            break;
    }
}

var intervalId = window.setInterval(function()
{
    CheckForNextBlock();
}, 10);

// 1. CREATE A LOOP THAT RUNS EVERY .1 SECONDS
// 2. IF THERE IS A BOOLEAN CALLED READY FOR NEXT BLOCK THEN START THE RUN NEXT BLOCK SCRIPT
// 3. BY DEFAULT THE BOOLEAN IS TRUE AND WILL BE MADE FALS THE MOMENT THE NEXT BLOCK SCRIPT IS CALLED AND BE MADE TRUE ONCE THE BLOCK HAS RAN TO COMPLETION
// 4. THE RESPONSE FROM EACH BLOCK WILL BE STORED IN THE INSTRUCTION OBJECT, THE INSTRUCTIONO BJECT WILL BE SEPERATED BY ROUTES
// 5. IF THE CURRENT ROUTE HAS NOT HAD A RESPONSE IN 5 SECONDS THE ROUTE WILL BE FREED