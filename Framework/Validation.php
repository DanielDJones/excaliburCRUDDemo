<?php
class Validation
{
    public $BOOL_Valid;
    public $ARR_Errors;
    public $ARR_Warnings;
    public $ARR_VariablesNotValid;

    function __construct()
    {
        $this->BOOL_Valid = TRUE;
        $this->ARR_Errors = [];
        $this->ARR_Warnings = [];
    }

    private function EnforceValidationRulesCallRule($STR_Rule, $STR_ValidationParameter, $STR_KeyToValidate)
    {
        # GUARD CLUASE FOR UNDEFINED RULE
        if(!method_exists($this, $STR_Rule)) {   $this->ARR_Warnings[] = 'Validation Rule does not exist {' . $STR_Rule . '}'; return; }

        # VALIDATE FIELD IN POST
        if(isset($_POST[$STR_KeyToValidate])) 
        { 
            # DETERMINE IF THE RULE IS VALID
            $INT_RuleValid = $this->$STR_Rule($_POST[$STR_KeyToValidate], $STR_ValidationParameter);

            if($INT_RuleValid === 0) 
            { 
                $this->ARR_Errors[] = 'Validation Rule {' . $STR_Rule . '} failed for {' . $STR_KeyToValidate . '}'; 
                $this->BOOL_Valid = FALSE; 
                $this->ARR_VariablesNotValid[] = $STR_KeyToValidate;
                return; 
            }
            if($INT_RuleValid === 2) 
            { 
                $this->ARR_Errors[] = 'Validation Rule {' . $STR_Rule . '} Had Internal Error With Key {' . $STR_KeyToValidate . '}'; 
                $this->BOOL_Valid = FALSE; 
                $this->ARR_VariablesNotValid[] = $STR_KeyToValidate;
                return; 
            }
        }

        # HANDLE FIELD IN GET
        if(isset($_GET[$STR_KeyToValidate])) 
        {
            # DETERMINE IF THE RULE IS VALID
            $INT_RuleValid = $this->$STR_Rule( $_GET[$STR_KeyToValidate], $STR_ValidationParameter);
            if($INT_RuleValid === 0) 
            { 
                $this->ARR_Errors[] = 'Validation Rule {' . $STR_Rule . '} failed for {' . $STR_KeyToValidate . '}'; 

                $this->BOOL_Valid = FALSE; 
                return; 
            }
            if($INT_RuleValid === 2) 
            { 
                $this->ARR_Errors[] = 'Validation Rule {' . $STR_Rule . '} Had Internal Error With Key {' . $STR_KeyToValidate . '}'; 
                $this->BOOL_Valid = FALSE; 
                return; 
            }
        }
    }

    public function EnforceValidationRules($STR_FieldName, $CSV_RulesToEnforce)
    {
        if($CSV_RulesToEnforce == NULL) { $this->ARR_Warnings[] = 'No Rules To Enforce'; return; }
        if($STR_FieldName == NULL) { $this->ARR_Warnings[] = 'No Field To Enforce Rules Upon'; return; }

        # FOREACH RULE
        $ARR_RulesToEnforce = explode(',', $CSV_RulesToEnforce);
        foreach($ARR_RulesToEnforce as $STR_Rule)
        {
            # IF VALIDATION RULE CONTAINS A : THEN IT HAS A PARAMETER (E.G. MaxLength:10)
            if(str_contains($STR_Rule, ':'))
            {
                
                # GET THE RULE AND PARAMETER
                $ARR_RuleAndParameter = explode(':', $STR_Rule);
                $STR_Rule = $ARR_RuleAndParameter[0];
                $STR_Parameter = $ARR_RuleAndParameter[1];

                # CALL THE VALIDATION RULE WITH THE PARAMETER
                $this-> EnforceValidationRulesCallRule($STR_Rule, $STR_Parameter, $STR_FieldName);
            }
            else
            {
                # CALL THE VALIDATION RULE WITHOUT THE PARAMETER
                $this-> EnforceValidationRulesCallRule($STR_Rule, NULL, $STR_FieldName);
            }
        }
        return;
    }

    public function RouteData($ARR_RouteValidationRules)
    {
        # GUARD CLAUSE
        // ?? REMOVE LATER, TESTING WITHOUT THIS CALUSE TO ALLOW ROUTES WITH NO DATA
        // if($ARR_RouteValidationRules == NULL) { $this->ARR_Errors[] = 'No Validation Rules Set!'; return; }

        # GUARD CLAUSE, EMPTY POST AND GET
        if($_POST == NULL && $_GET == NULL) { return; }

        # GET A LIST OF ALL THE PARAMETERS THAT ARE PERMITTED TO BE PASSED IN THE ROUTE
        $ARR_RouteAcceptedParameters = [];
        foreach($ARR_RouteValidationRules as $STR_Field => $CSV_FieldRules)
        {
            $ARR_RouteAcceptedParameters[] = $STR_Field;
        }

        # PURGE ALL POST DATA THAT ARE NOT IN THE LIST OF ACCEPTED PARAMETERS
        if($_POST != NULL)
        {
            foreach($_POST as $STR_Field => $STR_Value)
            {
                if(!in_array($STR_Field, $ARR_RouteAcceptedParameters))
                {
                    unset($_POST[$STR_Field]);
                }
            }
        }
        
        # PURGE ALL GET DATA THAT ARE NOT IN THE LIST OF ACCEPTED PARAMETERS
        if($_GET != NULL)
        {
            foreach($_GET as $STR_Field => $STR_Value)
            {
                if(!in_array($STR_Field, $ARR_RouteAcceptedParameters))
                {
                    unset($_GET[$STR_Field]);
                }
            }
        }

        # ENFORCE VALIDATION RULES
        foreach($ARR_RouteValidationRules as $STR_Field => $CSV_FieldRules)
        {
            $this->EnforceValidationRules($STR_Field, $CSV_FieldRules);
        }

        return;
    }

    /*
        ? VALIDATION RULES
        RESPONSE OF 0 = INVALID
        RESPONSE OF 1 = VALID
        RESPONSE OF 2 = ERROR WITH PARAMETERS
    */

    public function MaxLenght($STR_ToValidate, $INT_MaxDigits)
    {
        # GUARD CLAUSE FOR NO MAX DIGITS
        if($INT_MaxDigits == NULL) { return 2; }
        if(!is_int((int)$INT_MaxDigits)) { return 2; }

        # GUARD CLAUSE FOR NO STRING TO VALIDATE
        if($STR_ToValidate == NULL) { return 2; }
        
        # CHECK IF THE STRING IS LONGER THAN THE MAX DIGITS
        if(strlen($STR_ToValidate) > $INT_MaxDigits) { return 0; }

        # VALID
        return 1;
    }

    public function Required($STR_ToValidate)
    {
        # GUARD CLAUSE FOR NO STRING TO VALIDATE
        if($STR_ToValidate == NULL) { return 0; }

        # VALID
        return 1;
    }

    public function Optional($STR_ToValidate)
    {
        # VALID
        return 1;
    }
}

?>