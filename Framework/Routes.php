<?php

/*
    ? CREATES ROUTES THROUGH THE FILE STRUCTURE, TAKES THE URL AND RETURNS THE CORRECT FILE
*/

# DEFINE ROUTE HERE, METHOD, ROUTE = CONTROLLER / METHOD
$ARR_Routes = [];


# INDEX PAGE, FETCH ALL. THIS IS THE VERSION ON PAGELOAD THAT WILL WORK EVEN IF THE VISITOR HAS JS DISABLED
$ARR_Routes['POST']['guestbookpageload'][0] = ['PHP','GuestBookController@FetchAll',[]];

# ADD
# ADD 1, SEND DATA TO BACKEND TO ADD TO DB
$ARR_Routes['POST']['guestbookadd'][0] = ['PHP','GuestBookController@Insert',['name' => 'Optional', 'msg' => 'MaxLenght:2000,Required']];
# ADD 2, GET POST INFORMATION FROM DB
$ARR_Routes['POST']['guestbookadd'][1] = ['PHP','GuestBookController@GetLatestEntry',[]];
# ADD 3, IN JS ADD POST TO TOP OF THE PAGE
$ARR_Routes['POST']['guestbookadd'][2] = ['JS','GuestBookController@AddRowToUI',['latestrow' => 'Required']];

# EDIT
$ARR_Routes['POST']['guestbookshoweditform'][0] = ['PHP','GuestBookController@GetEntry',['id' => 'Required']];
$ARR_Routes['POST']['guestbookshoweditform'][1] = ['JS','GuestBookController@ShowEditForm',['entry' => 'Required']];

# VIEW

# DELETE
$ARR_Routes['POST']['guestbookdelete'][0] = ['PHP','GuestBookController@DeletePost',['id' => 'Required']];
$ARR_Routes['POST']['guestbookdelete'][1] = ['JS','GuestBookController@DeleteRowFromUI',['id' => 'Required']];

?>