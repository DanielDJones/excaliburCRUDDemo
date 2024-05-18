<?php

class GuestBookController extends Controller
{
    public function Insert($ARR_Chain)
    {
        # INSERT THE POST INTO THE DATABASE

        $STR_Name = $_POST['name'] != NULL ? $_POST['name'] : 'Anonymous';
        $STR_Message = $_POST['msg'];

        # INSERT INTO DB
        $OBJ_GuestBook = new GuestBook();
        $OBJ_GuestBook->Insert($STR_Name, $STR_Message);

        # RETURN THE FUNCTION
        return $this->CreateSuccessReturn($ARR_Chain);
    }

    public function GetLatestEntry($ARR_Chain)
    {
        # GET THE LATEST ENTRY FROM THE DATABASE, THIS WILL NOT SHOW THE LATEST REPONSE IF MULTIPLE PEOPLE ARE USING THE APP. FOR THE PURPOSE OF THIS DEMO IT WILL BE FINE

        # GET THE LATEST ENTRY
        $OBJ_GuestBook = new GuestBook();
        $ARR_LatestEntry = $OBJ_GuestBook->GetLatestEntry();

        # IF NO RESULTS JUST SHOW PASS A BLANK ARRAY BACK
        if(!is_array($ARR_LatestEntry))
        {
            $ARR_LatestEntry = [];
        }

        # PUSH LATEST ENTRY TO CHAIN
        $ARR_Chain['latestrow'] = $ARR_LatestEntry;

        # RETURN THE FUNCTION
        return $this->CreateSuccessReturn($ARR_Chain);
    }

    public function FetchAll($ARR_Chain)
    {
        # FETCH ALL ENTRIES FROM THE DATABASE
        $OBJ_GuestBook = new GuestBook();
        $ARR_AllEntries = $OBJ_GuestBook->GetAllEntries();

        if(!is_array($ARR_AllEntries))
        {
            $ARR_AllEntries = [];
        }

        # INITALISE STRING
        $HTML_Rows = '';

        if(count($ARR_AllEntries) > 0)
        {
            foreach($ARR_AllEntries as $INT_Key => $ARR_Entry)
            {
                # SANATISE THE DATA
                $INT_ID = htmlentities($ARR_Entry['ID']);
                $STR_Name = htmlentities($ARR_Entry['Name']);
                $STR_Message = htmlentities($ARR_Entry['Message']);

                # ADD ONTO HTML
                $HTML_Rows .= 
                '<section id="guestbookentry-'.$INT_ID.'" class="h-64 flex flex-row bg-slate-200">' . 
                    '<div class="flex flex-col w-1/4 max-w-32 justify-center gap-4 m-8">' . 
                        '<img class="shadow-gray-950 shadow" src="IMG/7.png" alt="">' .
                    '</div>' .
                    '<div class="flex w-full flex-col p-8">' .
                        '<h1 class="text-2xl font-bold my-8">' . $STR_Name . '</h1>' .
                        '<p class="text-ellipsis overflow-hidden">' . $STR_Message . '</p>' .
                    '</div>' .
                    '<div class="flex flex-col w-1/4 max-w-32 justify-center gap-4 m-8">' .
                        '<form method="post" id="Edit-'.$INT_ID.'"><input class="hidden" name="id" value="'.$INT_ID.'"><button type="button" class="bg-blue-600 text-gray-50 h-8 w-32" onclick="StartRoute(\'guestbookshoweditform\', \'Edit-'.$INT_ID.'\')">' .
                            'Edit' .
                        '</button></form>' .
                        '<form method="post" id="Delete-'.$INT_ID.'"><input class="hidden" name="id" value="'.$INT_ID.'"><button type="button" onclick="StartRoute(\'guestbookdelete\', \'Delete-'.$INT_ID.'\')" class="bg-red-600 text-gray-50 h-8 w-32">' .
                            'Delete' .
                        '</button></form>' .
                    '</div>' .
                '</section>';
            }
        }

        # PUSH HTML TO CHAIN
        $ARR_Chain['htmlrows'] = $HTML_Rows;

        # RETURN THE FUNCTION
        return $this->CreateSuccessReturn($ARR_Chain);
    }


    public function DeletePost($ARR_chain)
    {
        # DELETE THE POST FROM THE DATABASE
        $INT_ID = $_POST['id'];

        # DELETE FROM DB
        $OBJ_GuestBook = new GuestBook();
        $OBJ_GuestBook->Delete($INT_ID);

        # ADD ID BACK TO CHAIN TO ALLOW THE FRONTEND TO HIDE THE NOW DELETED POST
        $ARR_Chain['id'] = $INT_ID;

        # RETURN THE FUNCTION
        return $this->CreateSuccessReturn($ARR_Chain);
    }

    public function GetEntry($ARR_Chain)
    {
        # GET THE POST FROM THE DATABASE
        $INT_ID = $_POST['id'];

        # GET FROM DB
        $OBJ_GuestBook = new GuestBook();
        $ARR_Entry = $OBJ_GuestBook->GetEntry($INT_ID);

        # PUSH ENTRY TO CHAIN
        $ARR_Chain['entry'] = $ARR_Entry;
        
        # RETURN THE FUNCTION
        return $this->CreateSuccessReturn($ARR_Chain);
    }
}