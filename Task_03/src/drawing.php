<?php

interface Drawing
{
    public function startInfo($liquid, $constantMotion, $complexity);
    public function editSettingWall();
    public function editSettingMotion();
    public function editSettingComplexity();
    public function fillField($field, $height, $width, $char);
    public function drawField($h, $w, Gamer $gamer, array $characters);
    public function drawInfoField($x, $y, $n, $liquid, $constantMotion, $complexity);
    public function gameOver($h, $w, $liquid, $constantMotion, $complexity);
    public function drawResults(array $result = []);
}
