<?php

define("BUTTON_ADD", " <button class='add'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></button>");
define("BUTTON_REMOVE", " <button class='remove'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button>");
define("TD", '<td rowspan="%1$d" class = "%2$s"> %3$s');

$file = 'data/source.json';
$json = json_decode(file_get_contents($file), true);
//var_dump($json['roles'][0]['groups'][1]['options'][0]['resources'][0]);
//var_dump($json['roles'][0]['groups'][1]);
$html = '<h1 align="center">Table</h1>';
$html .= '<table align="center" ><thead><tr><th class="role">Roles:</th><th class="group">Groups:</th><th class="option">Options:</th><th class="resource">Resources:</th></tr></thead>';

foreach($json['roles'] as $role){
    $rowRole = 0;
    if (count($role) > 1){
        scanArray($role, $rowRole);
    }
    $html .= sprintf(TD, $rowRole, "role", $role['title']);
    $html .=  BUTTON_ADD.BUTTON_REMOVE. "</td>";
    if(isset ($role['groups'])){
        foreach($role['groups'] as $keyGroup => $group) {
            $rowGroup = 0;
            scanArray($group, $rowGroup);
            if ($keyGroup ===0){
                $html .= sprintf(TD, $rowGroup, "group", $group['title']);
                $html .=   BUTTON_ADD.BUTTON_REMOVE. "</td>";
                if (isset($group['options'])){
                    foreach($group['options'] as $keyOption => $option) {
                        $rowOption = 0;
                        scanArray($option, $rowOption);
                        if ($keyOption === 0) {
                            $html .= sprintf(TD, $rowOption, "option", $option['title']);
                            $html .=  BUTTON_ADD.BUTTON_REMOVE. "</td>";
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $rowResource = 0;
                                    scanArray($resource, $rowResource);
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, $rowResource, "resource", $resource['title']);
                                    $html .=  BUTTON_REMOVE. '</td>';
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                        } else {
                            $td = sprintf(TD, $rowOption, "option", $option['title']);
                            $html .= '<tr>'.$td. BUTTON_ADD.BUTTON_REMOVE. "</td>";
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $rowResource = 0;
                                    scanArray($resource, $rowResource);
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, $rowResource, "resource", $resource['title']);
                                    $html .= BUTTON_REMOVE. '</td>';
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                            $html .= '</tr>';
                        }
                    }
                }
            } else {
                $td = sprintf(TD, $rowGroup, "group", $group['title']);
                $html .= '<tr>'.$td. BUTTON_ADD.BUTTON_REMOVE. "</td>";
                if (isset($group['options'])){
                    foreach($group['options'] as $keyOption => $option) {
                        $rowOption = 0;
                        scanArray($option, $rowOption);
                        if ($keyOption === 0) {
                            $html .= sprintf(TD, $rowOption, "option", $option['title']);
                            $html .=  BUTTON_ADD.BUTTON_REMOVE. "</td>";
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $rowResource = 0;
                                    scanArray($resource, $rowResource);
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, $rowResource, "resource", $resource['title']);
                                    $html .= BUTTON_REMOVE. '</td>';
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                        } else {
                            $td = sprintf(TD, $rowOption, "option", $option['title']);
                            $html .= '<tr>'.$td. BUTTON_ADD.BUTTON_REMOVE. "</td>";
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $rowResource = 0;
                                    scanArray($resource, $rowResource);
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, $rowResource, "resource", $resource['title']);
                                    $html .= BUTTON_REMOVE. '</td>';
                                    $html .= ($keyResource === 0) ? '': '</tr>';
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
}
