<?php

interface Character
{
    public function getX();
    public function getY();
    public function getColor();
    public function moveCharacter($h, $w, $gamerX, $gamerY);
}
