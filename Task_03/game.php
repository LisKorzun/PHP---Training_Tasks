#!/usr/bin/php5
<?php

define("EXIT_KEY", 27);
define("H_INFO_FIELD", 10);
define("W_INFO_FIELD", 48);

Class Game
{
    public static $h = 15;
    public static $w = 40;
    public static $field;

    protected $hScreen;
    protected $wScreen;
    protected $infoField;
    protected $constantMotion = true;
    protected $liquid = false;
    protected $complexity = 1;

    protected function playGame()
    {
        ncurses_init();
        $this->setInitialSettings();
        $this->startInfo();
        ncurses_clear();
        $paddingTop = (($this->hScreen - self::$h) + H_INFO_FIELD) / 2;
        $paddingLeft = ($this->wScreen - self::$w) / 2;
        self::$field = ncurses_newwin(self::$h, self::$w, $paddingTop, $paddingLeft);
        ncurses_wcolor_set(self::$field, 1);
        ncurses_wborder(self::$field, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField(self::$field, self::$h, self::$w, '.');
    }

    protected function fillField($field, $height, $width, $char)
    {
        for ($i = 1; $i < $height - 1; $i++) {
            ncurses_mvwaddstr($field, $i, 1, str_pad($char, $width - 2, $char));
        }
    }

    private function setInitialSettings()
    {
        ncurses_clear();
        ncurses_curs_set(0);
        $this->wScreen = exec('tput cols');
        $this->hScreen = exec('tput lines');
        if (ncurses_has_colors()) {
            ncurses_start_color();
            ncurses_init_pair(1, NCURSES_COLOR_GREEN, NCURSES_COLOR_BLACK);
            ncurses_init_pair(2, NCURSES_COLOR_RED, NCURSES_COLOR_BLACK);
            ncurses_init_pair(3, NCURSES_COLOR_BLUE, NCURSES_COLOR_BLACK);
        }
    }

    private function startInfo()
    {
        $wall = ($this->liquid) ? 'прохода сквозь стены' : 'твердых стен';
        $motion = ($this->constantMotion) ? 'постоянного движения' : 'перемещения по нажатию';
        $this->infoField = ncurses_newwin(H_INFO_FIELD, W_INFO_FIELD, 0, ($this->wScreen - W_INFO_FIELD) / 2);
        ncurses_wcolor_set($this->infoField, 3);
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Добро пожаловать!');
        ncurses_mvwaddstr($this->infoField, 2, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 3, 2, $wall . ' и ' . $motion);
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Уровень сложности: '. $this->getComplexity());
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Управляйте персонажем стрелками');
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Для старта нажмите "ENTER"');
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для редактирования настроек нажмите "ПРОБЕЛ"');
        ncurses_refresh();
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 13) {
                break;
            } elseif ($pressed == 32) {
                $this->editSetting();
                break;
            }
        }
    }

    private function editSetting()
    {
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите режим стен:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '0 - твердые стены');
        ncurses_mvwaddstr($this->infoField, 5, 11, '1 - проход сквозь стены');
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 48) {
                $this->liquid = false;
                break;
            } elseif ($pressed == 49) {
                $this->liquid = true;
                break;
            }
        }
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите режим движения:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '0 - перемещение по нажатию');
        ncurses_mvwaddstr($this->infoField, 5, 11, '1 - постоянное движение');
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 48) {
                $this->constantMotion = false;
                break;
            } elseif ($pressed == 49) {
                $this->constantMotion = true;
                break;
            }
        }
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите уровень сложности:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '1 - легкий');
        ncurses_mvwaddstr($this->infoField, 5, 11, '2 - средний');
        ncurses_mvwaddstr($this->infoField, 6, 11, '3 - трудный');
        ncurses_wrefresh($this->infoField);
        while (true) {
            $pressed = ncurses_getch();
            switch($pressed){
                case 49:
                    $this->complexity = 1;
                    break 2;
                case 50:
                    $this->complexity = 3;
                    break 2;
                case 51:
                    $this->complexity = 5;
                    break 2;
            }
        }
    }

    protected function getInfo($x, $y, $n)
    {
        $wall = ($this->liquid) ? 'прохода сквозь стены' : 'твердых стен';
        $motion = ($this->constantMotion) ? 'постоянного движения' : 'перемещения по нажатию';
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 2, 2, $wall . ' и ' . $motion);
        ncurses_mvwaddstr($this->infoField, 3, 2, 'Уровень сложности: '. $this->getComplexity());
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Текущая координата x = ' . $x);
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Текущая координата y = ' . $y);
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Количество нажатий клавиш: ' . $n);
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для выхода из игры нажмите Esc');
        ncurses_wrefresh($this->infoField);
    }

    protected function gameOver(){
        $gameOverField = ncurses_newwin(H_INFO_FIELD, W_INFO_FIELD, ($this->hScreen - H_INFO_FIELD) / 2, ($this->wScreen - W_INFO_FIELD) / 2);
        ncurses_wcolor_set($gameOverField, 2);
        ncurses_wborder($gameOverField, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($gameOverField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($gameOverField, 3, 19, 'GAME OVER');
        ncurses_mvwaddstr($gameOverField, 6, 5, 'Чтобы сыграть еще раз нажмите "ENTER"');
        ncurses_mvwaddstr($gameOverField, 7, 5, 'Для выхода из игры нажмите "ESC"');
        ncurses_refresh();
        ncurses_wrefresh($gameOverField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 13) {
                $this->playGame();
                break;
            } elseif ($pressed == EXIT_KEY) {

                break 2;
            }
        }
    }

    private function getComplexity(){
        switch ($this->complexity){
            case 1:
                return 'легкий';
            case 3:
                return 'средний';
            case 5:
                return 'трудный';
        }
    }
}


Class Gamer extends Game
{
    const XRIGHT = 'xRight';
    const XLEFT = 'xLeft';
    const YUP = 'yUp';
    const YDOWN = 'yDown';

    public static $y;
    public static $x;
    private $counter = 0;

    public function playGame()
    {
        parent::playGame();
        self::$y = floor(Game::$h / 2);
        self::$x = floor(Game::$w / 2);
        ncurses_mvwaddstr(Game::$field, self::$y, self::$x, '@');
        ncurses_refresh();
        ncurses_wrefresh(Game::$field);
        $this->getInfo(self::$x, self::$y, $this->counter);
        $flag = '';
        for ($i = 0; $i < $this->complexity*2; $i++){
            $name = 'character'.$i;
            $$name = ($i < $this->complexity) ? CharacterFactory::create("PursueCharacter") : CharacterFactory::create("RandCharacter");
        }

        while (true) {
            $pressed = ($this->constantMotion) ? Utils::getch_nonblock(1000000) : ncurses_getch();
            switch ($pressed) {
                case EXIT_KEY:
                    break 2;
                case NCURSES_KEY_RIGHT:
                    $this->checkRightBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_LEFT:
                    $this->checkLeftBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_UP:
                    $this->checkTopBorder($flag);
                    $this->counter++;
                    break;
                case NCURSES_KEY_DOWN:
                    $this->checkBottomBorder($flag);
                    $this->counter++;
                    break;
                default:
                    if ($this->constantMotion) {
                        switch ($flag) {
                            case self::XRIGHT:
                                $this->checkRightBorder($flag);
                                break 2;
                            case self::XLEFT:
                                $this->checkLeftBorder($flag);
                                break 2;
                            case self::YUP:
                                $this->checkTopBorder($flag);
                                break 2;
                            case self::YDOWN:
                                $this->checkBottomBorder($flag);
                                break 2;
                            default:
                                $this->checkTopBorder($flag);
                                break 2;
                        }
                    }
            }
            $this->fillField(Game::$field, Game::$h, Game::$w, '.');
            ncurses_mvwaddstr(Game::$field, self::$y, self::$x, '@');
            for ($i = 0; $i < $this->complexity*2; $i++){
                $name = 'character'.$i;
                $$name->moveCharacter();
            }

            $this->getInfo(self::$x, self::$y, $this->counter);
            ncurses_wrefresh(Game::$field);
            for ($i = 0; $i < $this->complexity*2; $i++){
                $name = 'character'.$i;
                if (self::$x == $$name->getX() AND self::$y == $$name->getY()){
                    ncurses_clear();
                    $this->gameOver();
                }
            }
        }
        ncurses_end();
    }

    private function CheckRightBorder(&$flag)
    {
        if (self::$x == Game::$w - 2) {
            if ($this->liquid) {
                $flag = self::XRIGHT;
                self::$x = 1;
            } else {
                $flag = self::XLEFT;
                ($this->constantMotion) ? self::$x-- : self::$x;
            }
        } else {
            $flag = self::XRIGHT;
            self::$x++;
        }
        return self::$x;
    }

    private function checkLeftBorder(&$flag)
    {
        if (self::$x == 1) {
            if ($this->liquid) {
                $flag = self::XLEFT;
                self::$x = Game::$w - 2;
            } else {
                $flag = self::XRIGHT;
                ($this->constantMotion) ? self::$x++ : self::$x;
            }
        } else {
            $flag = self::XLEFT;
            self::$x--;
        }
        return self::$x;
    }

    private function checkTopBorder(&$flag)
    {
        if (self::$y == 1) {
            if ($this->liquid) {
                $flag = self::YUP;
                self::$y = Game::$h - 2;
            } else {
                $flag = self::YDOWN;
                ($this->constantMotion) ? self::$y++ : self::$y;
            }
        } else {
            $flag = self::YUP;
            self::$y--;
        }
        return self::$y;
    }

    protected function checkBottomBorder(&$flag)
    {
        if (self::$y == Game::$h - 2) {
            if ($this->liquid) {
                $flag = self::YDOWN;
                self::$y = 1;
            } else {
                $flag = self::YUP;
                ($this->constantMotion) ? self::$y-- : self::$y;
            }
        } else {
            $flag = self::YDOWN;
            self::$y++;
        }
        return self::$y;
    }
}

Class Utils
{
    /**
     * Функция необходима для для решения проблем с блокированием потока для ncurses_getch
     * http://php.net/manual/en/function.ncurses-getch.php
     *
     * @param $timeout
     * @return int|null
     */
    static public function getch_nonblock($timeout)
    {
        $read = array(STDIN);
        $null = null;    // stream_select() uses references, thus variables are necessary for the first 3 parameters
        if (stream_select($read, $null, $null, floor($timeout / 1000000), $timeout % 1000000) != 1) return null;
        return ncurses_getch();
    }
}

interface Character
{
    public function getX();
    public function getY();
    public function moveCharacter();
}

Class RandCharacter implements Character
{
    private $x;
    private $y;

    public function __construct()
    {
        $this->y = rand(1, Game::$h - 1);
        $this->x = rand(1, Game::$w - 1);
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function moveCharacter()
    {
        ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '*');
        $rand = rand(1, 4);
        switch ($rand) {
            case 1:
                if ($this->x == Game::$w - 2) {
                    $this->x--;
                } else {
                    $this->x++;
                }
                break 1;
            case 2:
                if ($this->x == 1) {
                    $this->x++;
                } else {
                    $this->x--;
                }
                break 1;
            case 3:
                if ($this->y == Game::$h - 2) {
                    $this->y--;
                } else {
                    $this->y++;
                }
                break 1;
            case 4:
                if ($this->y == 1) {
                    $this->y++;
                } else {
                    $this->y--;
                }
                break 1;
        }
    }
}

Class PursueCharacter implements Character
{
    public $x;
    public $y;

    public function __construct()
    {
        $this->y = rand(1, Game::$h - 1);
        $this->x = rand(1, Game::$w - 1);
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function moveCharacter()
    {
        $rangeX =[];
        $rangeY = [];
        ncurses_mvwaddstr(Game::$field, $this->y, $this->x, '*');
        for ($i = $this->x-5; $i <$this->x+5; $i++){
            $rangeX[] = $i;
        }
        for ($i = $this->y-5; $i <$this->y+5; $i++){
            $rangeY[] = $i;
        }
        if (in_array(Gamer::$x, $rangeX) AND in_array(Gamer::$y, $rangeY))
        {
            if ( abs($this->x - Gamer::$x) >= abs($this->y - Gamer::$y)){
                ($this->x > Gamer::$x) ? $this->x-- : $this->x++;
            } else {
                ($this->y > Gamer::$y) ? $this->y-- : $this->y++;
            }
        } else {
            $rand = rand(1, 4);
            switch ($rand) {
                case 1:
                    if ($this->x == Game::$w - 2) {
                        $this->x--;
                    } else {
                        $this->x++;
                    }
                    break 1;
                case 2:
                    if ($this->x == 1) {
                        $this->x++;
                    } else {
                        $this->x--;
                    }
                    break 1;
                case 3:
                    if ($this->y == Game::$h - 2) {
                        $this->y--;
                    } else {
                        $this->y++;
                    }
                    break 1;
                case 4:
                    if ($this->y == 1) {
                        $this->y++;
                    } else {
                        $this->y--;
                    }
                    break 1;
            }
        }
    }
}


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

$gamer = new Gamer();
$gamer->playGame();
