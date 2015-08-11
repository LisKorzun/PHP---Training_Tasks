<?php

define("BUTTON_ADD", " <button class='add'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></button>");
define("BUTTON_REMOVE", " <button class='remove'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></button>");
define("TD", '<td rowspan="%1$d" class = "%2$s"> %3$s %4$s </td>');

$file = 'data/source.json';
$json = json_decode(file_get_contents($file), true);

$html = '<h1 align="center">Table</h1>';
$html .= '<table align="center" ><thead><tr>
            <th class="role">Roles:</th>
            <th class="group">Groups:</th>
            <th class="option">Options:</th>
            <th class="resource">Resources:</th>
        </tr></thead>';

foreach($json['roles'] as $role){
    if (count($role) > 1){
        $rowRole = countRowSpan($role);
    }
    $html .= sprintf(TD, $rowRole, "role", $role['title'], BUTTON_ADD.BUTTON_REMOVE);
    if(isset ($role['groups'])){
        foreach($role['groups'] as $keyGroup => $group) {
            $rowGroup =  countRowSpan($group);
            if ($keyGroup ===0){
                $html .= sprintf(TD, $rowGroup, "group", $group['title'], BUTTON_ADD.BUTTON_REMOVE);
                if (isset($group['options'])){
                    foreach($group['options'] as $keyOption => $option) {
                        $rowOption =  countRowSpan($option);
                        if ($keyOption === 0) {
                            $html .= sprintf(TD, $rowOption, "option", $option['title'], BUTTON_ADD.BUTTON_REMOVE);
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, 1, "resource", $resource['title'], BUTTON_REMOVE);
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                        } else {
                            $td = sprintf(TD, $rowOption, "option", $option['title'], BUTTON_ADD.BUTTON_REMOVE);
                            $html .= '<tr>'.$td;
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, 1, "resource", $resource['title'], BUTTON_REMOVE);
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                            $html .= '</tr>';
                        }
                    }
                }
            } else {
                $html .= '<tr>'.sprintf(TD, $rowGroup, "group", $group['title'], BUTTON_ADD.BUTTON_REMOVE);
                if (isset($group['options'])){
                    foreach($group['options'] as $keyOption => $option) {
                        $rowOption = countRowSpan($option);
                        if ($keyOption === 0) {
                            $html .= sprintf(TD, $rowOption, "option", $option['title'], BUTTON_ADD.BUTTON_REMOVE);
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, 1, "resource", $resource['title'], BUTTON_REMOVE);
                                    $html .= ($keyResource === 0) ? '': '</tr>';
                                }
                            }
                        } else {
                            $html .= '<tr>'.sprintf(TD, $rowOption, "option", $option['title'], BUTTON_ADD.BUTTON_REMOVE);
                            if (isset($option['resources'])){
                                foreach($option['resources'] as $keyResource=> $resource) {
                                    $html .= ($keyResource === 0) ? '': '<tr>';
                                    $html .= sprintf(TD, 1, "resource", $resource['title'], BUTTON_REMOVE);
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

/**
 * Считает количество rowSpan
 * @param array $data
 */
function countRowSpan(array $data)
{
    $rowSpan = 0;
    foreach($data as $key => $item){
        if($key === 'title'){
            if (count($data) == 1){
                ++$rowSpan;
            }
            continue;
        }
        if(is_array($item)){
            $rowSpan += countRowSpan($item);
        }
    }
    return $rowSpan;
}
