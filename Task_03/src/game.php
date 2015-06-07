<?php

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
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Уровень сложности: ' . $this->getComplexity());
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
            switch ($pressed) {
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
        ncurses_mvwaddstr($this->infoField, 3, 2, 'Уровень сложности: ' . $this->getComplexity());
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Текущая координата x = ' . $x);
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Текущая координата y = ' . $y);
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Количество нажатий клавиш: ' . $n);
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для выхода из игры нажмите Esc');
        ncurses_wrefresh($this->infoField);
    }

    protected function gameOver()
    {
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

    private function getComplexity()
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
}
