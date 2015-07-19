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
            unset($jsonArray['roles'][$indexRole]);
        } elseif ($indexOption == 'no') {
            unset($jsonArray['roles'][$indexRole]['groups'][$indexGroup]);
        } elseif ($indexResource == 'no'){
            unset($jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'][$indexOption]);
        } else{
            unset($jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'][$indexOption]['resources'][$indexResource]);
        }

        $json = json_encode($jsonArray);
        file_put_contents($file, json_encode($jsonArray));
    } catch (Exception $e){
        echo $e;
    }
}
