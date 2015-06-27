<?php

Class Game
{
    private $h = 15;
    private $w = 40;
    private $constantMotion = true;
    private $liquid = false;
    private $complexity = 3;
    private $painter;
    private $listener;
    private $gamer;
    private $characters;

    public function setPainter(Drawing $painter)
    {
        $this->painter = $painter;
    }

    public function setListener(Listener $listener)
    {
        $this->listener = $listener;
    }

    public function setGamer(Gamer $gamer)
    {
        $this->gamer = $gamer;
    }

    public function getH()
    {
        return $this->h;
    }

    public function getW()
    {
        return $this->w;
    }

    public function getComplexity()
    {
        switch ($this->complexity) {
            case 1:
                return 'легкий';
            case 3:
                return 'средний';
            case 5:
                return 'трудный';
        }
    }

    public function playGame()
    {
       try {
           $db = Db::getInstance();
           $winners = $db->getWinners();
       } catch (PDOException $e){
           die ('Exception: '.$e->getMessage());
       }
        $this->painter->startInfo($this->liquid, $this->constantMotion, $this->getComplexity());
        if(!empty($winners)){
            $this->painter->drawResults($winners);
        }
        $kea = $this->listener->controlUserAction(array(ENTER_KEY, SPACE_KEY));
        if ($kea == SPACE_KEY) {
            $this->painter->editSettingWall();
            $pressed = $this->listener->controlUserAction(array(NULL_KEY, ONE_KEY));
            if ($pressed == NULL_KEY) {
                $this->liquid = false;
                $this->gamer->setLiquid(false);
            } elseif ($pressed == ONE_KEY) {
                $this->gamer->setLiquid(true);
                $this->liquid = true;
            }
            $this->painter->editSettingMotion();
            $pressed = $this->listener->controlUserAction(array(NULL_KEY, ONE_KEY));
            if ($pressed == NULL_KEY) {
                $this->constantMotion = false;
                $this->gamer->setConstantMotion(false);
            } elseif ($pressed == ONE_KEY) {
                $this->gamer->setConstantMotion(true);
                $this->constantMotion = true;
            }
            $this->painter->editSettingComplexity();
            $pressed = $this->listener->controlUserAction(array(ONE_KEY, TWO_KEY, THREE_KEY));
            switch ($pressed) {
                case ONE_KEY:
                    $this->complexity = 1;
                    break;
                case TWO_KEY:
                    $this->complexity = 3;
                    break;
                case THREE_KEY:
                    $this->complexity = 5;
                    break;
            }
        }
        $this->painter->drawInfoField($this->gamer->getX(), $this->gamer->getY(), $this->gamer->getCounter(), $this->liquid, $this->constantMotion, $this->getComplexity());
        $this->characters = [];
        for ($i = 0; $i < $this->complexity * 2; $i++) {
            $this->characters[] = ($i < $this->complexity) ? CharacterFactory::create("CharacterPursue") : CharacterFactory::create("CharacterRand");
        }
        $this->painter->drawField($this->h, $this->w, $this->gamer, $this->characters);
        $flag = '';
        while (true) {
            if ($this->constantMotion){
                $kea = $this->listener->controlUserAction(array(ListenerNcurses::RIGHT_KEY, ListenerNcurses::LEFT_KEY, ListenerNcurses::UP_KEY, ListenerNcurses::DOWN_KEY, EXIT_KEY), true);
            } else {
                $kea = $this->listener->controlUserAction(array(ListenerNcurses::RIGHT_KEY, ListenerNcurses::LEFT_KEY, ListenerNcurses::UP_KEY, ListenerNcurses::DOWN_KEY, EXIT_KEY));
            }
            switch ($kea) {
                case EXIT_KEY:
                    break 2;
                case ListenerNcurses::RIGHT_KEY:
                    $this->gamer->checkRightBorder($flag);
                    $this->gamer->increaseCounter();
                    break;
                case ListenerNcurses::LEFT_KEY:
                    $this->gamer->checkLeftBorder($flag);
                    $this->gamer->increaseCounter();
                    break;
                case ListenerNcurses::UP_KEY:
                    $this->gamer->checkTopBorder($flag);
                    $this->gamer->increaseCounter();
                    break;
                case ListenerNcurses::DOWN_KEY:
                    $this->gamer->checkBottomBorder($flag);
                    $this->gamer->increaseCounter();
                    break;
                default:
                    if ($this->constantMotion) {
                        switch ($flag) {
                            case XRIGHT:
                                $this->gamer->checkRightBorder($flag);
                                break 2;
                            case XLEFT:
                                $this->gamer->checkLeftBorder($flag);
                                break 2;
                            case YUP:
                                $this->gamer->checkTopBorder($flag);
                                break 2;
                            case YDOWN:
                                $this->gamer->checkBottomBorder($flag);
                                break 2;
                            default:
                                $this->gamer->checkTopBorder($flag);
                                break 2;
                        }
                    }
            }
            foreach ($this->characters as $character) {
                $character->moveCharacter($this->h, $this->w, $this->gamer->getX(), $this->gamer->getY());
            }
            $this->painter->drawField($this->h, $this->w, $this->gamer, $this->characters);
            $this->painter->drawInfoField($this->gamer->getX(), $this->gamer->getY(), $this->gamer->getCounter(), $this->liquid, $this->constantMotion, $this->getComplexity());
            foreach ($this->characters as $character) {
                if ($character->getX() == $this->gamer->getX() AND $character->getY() == $this->gamer->getY()) {
                    ncurses_clear();
                    $this->painter->gameOver();
                    $keyList = [13];
                    for ($i = 48; $i < 58; $i++) {
                        $keyList[] = $i;
                    }
                    for ($i = 65; $i < 91; $i++) {
                        $keyList[] = $i;
                    }
                    for ($i = 97; $i < 123; $i++) {
                        $keyList[] = $i;
                    }
                    $keys = [];
                    while (true) {
                        $this->painter->echoUserName();
                        $key = $this->listener->controlUserAction($keyList);
                        if ($key != 13) {
                            $keys[] = chr($key);
                        }
                        if ($key == 13 OR count($keys) >= 20){
                            $name = implode('', $keys);
                            break 3;
                        }
                    }
                }
            }
        }

        try {
            $db->savePlayer($name,$this->gamer->getCounter());
            $winners = $db->getWinners();
        } catch (PDOException $e){
            die ('Exception: '.$e->getMessage());
        }

        $db = NULL;
        $this->painter->drawResults($winners);
        $this->listener->controlUserAction();
    }
}
