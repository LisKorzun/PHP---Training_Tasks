<?php

interface Character
{
    public function getX();
    public function getY();
    public function moveCharacter($h, $w, $gamerX, $gamerY);
}
