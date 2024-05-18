console.warn('LOADING CONTROLLER');

window['GuestBookController'] = class GuestBookController {

    AddRowToUI(OBJ_ChainedVars)
    {
        // THIS IS HOW YOUD SET VARIABLES TO THE CHAIN IN JS
        // OBJ_ChainedVars.FrontEndTestVar = 'Im Fron Javascript Woo!';

        // MAIN LOGIC HERE
        if(OBJ_ChainedVars.latestrow != null && OBJ_ChainedVars.latestrow != undefined)
        {
            if(OBJ_ChainedVars.latestrow.ID != undefined)
            {
                var HTML_NewRow = '<section class="h-64 flex flex-row bg-slate-200">';
                        HTML_NewRow += '<div class="flex flex-col w-1/4 max-w-32 justify-center gap-4 m-8">';
                            HTML_NewRow += '<img class="shadow-gray-950 shadow" src="IMG/7.png" alt="">';
                        HTML_NewRow += '</div>';
                        HTML_NewRow += '<div class="flex w-full flex-col p-8">';
                            HTML_NewRow += '<h1 class="text-2xl font-bold my-8">' + OBJ_ChainedVars.latestrow.Name + '</h1>';
                        HTML_NewRow += '<p class="text-ellipsis overflow-hidden">' + OBJ_ChainedVars.latestrow.Message + '</p>';
                        HTML_NewRow += '</div>';
                        HTML_NewRow += '<div class="flex flex-col w-1/4 max-w-32 justify-center gap-4 m-8">';
                            HTML_NewRow += '<button type="button" class="bg-blue-600 text-gray-50 h-8 w-32">';
                                HTML_NewRow += 'Edit';
                            HTML_NewRow += '</button>';
                            HTML_NewRow += '<button type="button" class="bg-red-600 text-gray-50 h-8 w-32">';
                                HTML_NewRow += 'Delete';
                            HTML_NewRow += '</button>';
                        HTML_NewRow += '</div>';
                    HTML_NewRow += '</section>';
                ;

                var NODE_GuestBookEntries = document.getElementById('guestbookentries');
                NODE_GuestBookEntries.innerHTML = HTML_NewRow + NODE_GuestBookEntries.innerHTML;
            }
        }

        // RETURN
        var OBJ_Return = {};
        OBJ_Return['OBJ_ChainedVars'] = OBJ_ChainedVars;
        OBJ_Return['INT_STATUS'] = 1;
        OBJ_Return['STR_MSG'] = 'Success';

        return OBJ_Return;
    }

    DeleteRowFromUI(OBJ_ChainedVars)
    {
        if(OBJ_ChainedVars.id == null || OBJ_ChainedVars.id == undefined)
        {
            // GUARD CLAUSE, NO ID GIVEN
            var OBJ_Return = {};
            OBJ_Return['OBJ_ChainedVars'] = OBJ_ChainedVars;
            OBJ_Return['INT_STATUS'] = 1;
            OBJ_Return['STR_MSG'] = 'Success';

            return OBJ_Return;
        }

        // MAIN LOGIC HERE
        if(OBJ_ChainedVars.id != null || undefined)
        {
            var STR_UIReference = 'guestbookentry-' + OBJ_ChainedVars.id;
            var NODE_GuestBookEntries = document.getElementById(STR_UIReference);
            console.log('NODE_GuestBookEntries ', NODE_GuestBookEntries);
            NODE_GuestBookEntries.classList.add("hidden");
        }

        // RETURN
        var OBJ_Return = {};
        OBJ_Return['OBJ_ChainedVars'] = OBJ_ChainedVars;
        OBJ_Return['INT_STATUS'] = 1;
        OBJ_Return['STR_MSG'] = 'Success';

        return OBJ_Return;
    }

    ShowEditForm(OBJ_ChainedVars)
    {
        // MAIN LOGIC HERE
        if(OBJ_ChainedVars.entry != null && OBJ_ChainedVars.entry != undefined)
        {
            if(OBJ_ChainedVars.entry.ID != undefined)
            {
                var NODE_EditForm = document.getElementById('editpost');
                var NODE_EditID = document.getElementById('edit-id');
                var NODE_EditName = document.getElementById('edit-name');
                var NODE_EditMessage = document.getElementById('edit-msg');

                // SHOW FORM
                NODE_EditForm.classList.remove('hidden');

                // SET VALUES
                NODE_EditID.value = OBJ_ChainedVars.entry.ID;
                NODE_EditName.value = OBJ_ChainedVars.entry.Name;
                NODE_EditMessage.value = OBJ_ChainedVars.entry.Message;
            }
            var STR_UIReference = 'guestbookentry-' + OBJ_ChainedVars.id;
            var NODE_GuestBookEntries = document.getElementById(STR_UIReference);
            console.log('NODE_GuestBookEntries ', NODE_GuestBookEntries);
            NODE_GuestBookEntries.classList.add("hidden");
        }

        // RETURN
        var OBJ_Return = {};
        OBJ_Return['OBJ_ChainedVars'] = OBJ_ChainedVars;
        OBJ_Return['INT_STATUS'] = 1;
        OBJ_Return['STR_MSG'] = 'Success';

        return OBJ_Return;
    }
    
}
