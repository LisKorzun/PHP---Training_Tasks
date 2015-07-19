<?php
if (isset($_POST['indexRole']) AND
    isset($_POST['indexGroup']) AND
    isset($_POST['indexOption']) AND
    isset($_POST['indexResource'])){

    $indexRole = $_POST['indexRole'];
    $indexGroup = $_POST['indexGroup'];
    $indexOption = $_POST['indexOption'];
    $indexResource = $_POST['indexResource'];
    try {
        $file = '../data/source.json';
        $jsonArray = json_decode(file_get_contents($file), true);
        if ($indexGroup == 'no') {
            array_splice($jsonArray['roles'],$indexRole, 1);
        } elseif ($indexOption == 'no') {
            array_splice($jsonArray['roles'][$indexRole]['groups'], $indexGroup, 1);
        } elseif ($indexResource == 'no'){
            array_splice($jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'],$indexOption, 1);
        } else{
            array_splice($jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'][$indexOption]['resources'],$indexResource, 1);
        }
        $json = json_encode($jsonArray);
        file_put_contents($file, json_encode($jsonArray));
    } catch (Exception $e){
        echo $e;
    }
}
