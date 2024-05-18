<?php
class GuestBook extends Model
{
    public function Insert($STR_Name, $STR_Message)
    {
        $ARR_SQLParams = [];
        $ARR_SQLParams['Name'] = $STR_Name;
        $ARR_SQLParams['Message'] = $STR_Message;

        $SQL_Query = "INSERT INTO GuestBook (Name, Message) VALUES (:Name, :Message)";
        $OBJ_Query = $this->OBJ_PDO->prepare($SQL_Query);
        $OBJ_Query->execute($ARR_SQLParams);
        return;
    }

    public function GetLatestEntry()
    {
        $SQL_Query = "SELECT * FROM GuestBook ORDER BY ID DESC LIMIT 1";
        $OBJ_Query = $this->OBJ_PDO->prepare($SQL_Query);
        $OBJ_Query->execute();
        $ARR_LatestEntry = $OBJ_Query->fetch(PDO::FETCH_ASSOC);
        return $ARR_LatestEntry;
    }

    public function GetEntry($INT_ID)
    {
        $ARR_SQLParams = [];
        $ARR_SQLParams['ID'] = $INT_ID;

        $SQL_Query = "SELECT * FROM GuestBook WHERE ID = :ID";
        $OBJ_Query = $this->OBJ_PDO->prepare($SQL_Query);
        $OBJ_Query->execute($ARR_SQLParams);
        $ARR_Entry = $OBJ_Query->fetch(PDO::FETCH_ASSOC);
        return $ARR_Entry;
    }

    public function GetAllEntries()
    {
        $SQL_Query = "SELECT * FROM GuestBook ORDER BY ID DESC";
        $OBJ_Query = $this->OBJ_PDO->prepare($SQL_Query);
        $OBJ_Query->execute();
        $ARR_LatestEntry = $OBJ_Query->fetchAll(PDO::FETCH_ASSOC);
        return $ARR_LatestEntry;
    }

    public function Delete($INT_ID)
    {
        $ARR_SQLParams = [];
        $ARR_SQLParams['ID'] = $INT_ID;

        $SQL_Query = "DELETE FROM GuestBook WHERE ID = :ID";
        $OBJ_Query = $this->OBJ_PDO->prepare($SQL_Query);
        $OBJ_Query->execute($ARR_SQLParams);
        return;
    }


}