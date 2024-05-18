<?php

class Controller
{
    
    public function CreateSuccessReturn($ARR_Chain, $STR_Message = 'Success')
    {
        $ARR_Return = [];
        $ARR_Return['INT_STATUS'] = 1; # GIVE A SUCESS STATUS
        $ARR_Return['STR_MSG'] = $STR_Message; # GIVE A SUCCESS MESSAGE
        $ARR_Return['ARR_Chain'] = $ARR_Chain; # UPDATE THE CHAIN
        return $ARR_Return;
    }
}

?>