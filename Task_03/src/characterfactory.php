<?php

class CharacterFactory
{
    public static function create($character)
    {
        if (class_exists($character)) {
            return new $character();
        } else {
            throw new \Exception("Такого персонажа не существует.");
        }
    }
}
