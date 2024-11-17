<?php
include("php/config.php"); 

//fetch data from the TestResult table 

$query = "SELECT * FROM testresult";
$result = mysqli_query($conn,$query); 

$data=[];

if($result)
{
    while($row = mysqli_fetch_assoc($result))
    {
        $data[] = $row; 
    }

    echo json_encode($data); //encode data as JSON for javascript to use
}
else
{
    echo json_encode(["error" => mysqli_error($conn)]);
}

?>