<?php
if (isset($_POST['indexRole']) AND
    isset($_POST['indexGroup']) AND
    isset($_POST['indexOption']) AND
    isset($_POST['title'])){

    $indexRole = $_POST['indexRole'];
    $indexGroup = $_POST['indexGroup'];
    $indexOption = $_POST['indexOption'];
    $title = $_POST['title'];
    try {
        $file = '../data/source.json';
        $jsonArray = json_decode(file_get_contents($file), true);

        if ($indexGroup == 'no') {
            $jsonArray['roles'][$indexRole]['groups'][] = array('title' => $title);
        } elseif ($indexOption == 'no') {
            $jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'][] = array('title' => $title);
        } else {
            $jsonArray['roles'][$indexRole]['groups'][$indexGroup]['options'][$indexOption]['resources'][] = array('title' => $title);
        }

        $json = json_encode($jsonArray);
        file_put_contents($file, json_encode($jsonArray));
    } catch (Exception $e){
        echo $e;
    }
}
