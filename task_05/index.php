<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Table-Tree</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <?php
    $file = 'data/source.json';
    $json = json_decode(file_get_contents($file), true);
    $html = '<p align="center">Table</p>';
    $html .= '<table align="center" ><thead><tr><th class="role">Roles:</th><th class="group">Groups:</th><th class="option">Options:</th><th class="resource">Resources:</th></tr></thead>';
    foreach($json['roles'] as $role){
        $rowRole = 0;
        if (count($role) > 1){
             $rowSpan = scanArray($role, $rowRole);
        }
        $html .=  '<tr><td rowspan="'.$rowRole.'" class = "role">'.$role['title'];
        $html .=  " <button class='add'>+</button></td>";
        if(isset ($role['groups'])){
            foreach($role['groups'] as $keyGroup => $group) {
                $rowGroup = 0;
                scanArray($group, $rowGroup);
                if ($keyGroup ===0){
                    $html .=  '<td rowspan="'.$rowGroup.'" class = "group">'.$group['title'];
                    $html .=  " <button class='add'>+</button></td>";
                    if (isset($group['options'])){
                        foreach($group['options'] as $keyOption => $option) {
                            $rowOption = 0;
                            scanArray($option, $rowOption);
                            if ($keyOption === 0) {
                                $html .= '<td rowspan="' . $rowOption . '" class = "option">' . $option['title'];
                                $html .= " <button class='add'>+</button></td>";
                                if (isset($option['resources'])){
                                    foreach($option['resources'] as $keyResource=> $resource) {
                                        $rowResource = 0;
                                        scanArray($resource, $rowResource);
                                        if ($keyResource === 0) {
                                            $html .= '<td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td>";
                                        } else{
                                            $html .= '<tr><td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td></tr>";
                                        }
                                    }
                                }
                            } else {
                                $html .= '<tr><td rowspan="' . $rowOption . '" class = "option">' . $option['title']. " <button class='add'>+</button></td>";
                                if (isset($option['resources'])){
                                    foreach($option['resources'] as $keyResource=> $resource) {
                                        $rowResource = 0;
                                        scanArray($resource, $rowResource);
                                        if ($keyResource === 0) {
                                            $html .= '<td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td>";
                                        } else{
                                            $html .= '<tr><td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td></tr>";
                                        }
                                    }
                                }
                                $html .= '</tr>';
                            }
                        }
                    }
                } else {
                    $html .=  '<tr><td rowspan="'.$rowGroup.'" class = "group">'.$group['title']." <button class='add'>+</button></td>";
                    if (isset($group['options'])){
                        foreach($group['options'] as $keyOption => $option) {
                            $rowOption = 0;
                            scanArray($option, $rowOption);
                            if ($keyOption === 0) {
                                $html .= '<td rowspan="' . $rowOption . '" class = "option">' . $option['title'];
                                $html .= " <button class='add'>+</button></td>";
                                if (isset($option['resources'])){
                                    foreach($option['resources'] as $keyResource=> $resource) {
                                        $rowResource = 0;
                                        scanArray($resource, $rowResource);
                                        if ($keyResource === 0) {
                                            $html .= '<td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td>";
                                        }else{
                                            $html .= '<tr><td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td></tr>";
                                        }
                                    }
                                }
                            } else {
                                $html .= '<tr><td rowspan="' . $rowOption . '" class = "option">' . $option['title']. " <button class='add'>+</button></td>";
                                if (isset($option['resources'])){
                                    foreach($option['resources'] as $keyResource=> $resource) {
                                        $rowResource = 0;
                                        scanArray($resource, $rowResource);
                                        if ($keyResource === 0) {
                                            $html .= '<td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td>";
                                        }else{
                                            $html .= '<tr><td rowspan="' . $rowResource . '" class = "resource">' . $resource['title'];
                                            $html .= "</td></tr>";
                                        }
                                    }
                                }
                                $html .= '</tr>';
                            }
                        }
                    }
                    $html .=  "</tr>";
                }
            }
        }
        $html .=  "</tr>";
    }
    $html .=  "</table>";
    echo $html;

    function scanArray(array $data, &$rowSpan)
    {
        foreach($data as $key => $item){
            if($key === 'title'){
                if (count($data) == 1){
                    $rowSpan += count($data);
                }
                continue;
            }
            if(is_array($item)){
                scanArray($item, $rowSpan);
            }
        }
        return $rowSpan;
    }
    ?>
</div>

<script src="js/script.js"></script>
</body>
</html>