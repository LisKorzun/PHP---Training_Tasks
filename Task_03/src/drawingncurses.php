<?php

Class DrawingNcurses implements Drawing
{
    private $field;
    private $hScreen;
    private $wScreen;
    private $infoField;
    
    public function __construct()
    {
        ncurses_init();
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

    public function fillField($field, $height, $width, $char)
    {
        for ($i = 1; $i < $height - 1; $i++) {
            ncurses_mvwaddstr($field, $i, 1, str_pad($char, $width - 2, $char));
        }
    }

    public function startInfo($liquid, $constantMotion, $complexity)
    {
        $wall = ($liquid) ? 'прохода сквозь стены' : 'твердых стен';
        $motion = ($constantMotion) ? 'постоянного движения' : 'перемещения по нажатию';
        $this->infoField = ncurses_newwin(H_INFO_FIELD, W_INFO_FIELD, 0, ($this->wScreen - W_INFO_FIELD) / 2);
        ncurses_wcolor_set($this->infoField, 3);
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Добро пожаловать!');
        ncurses_mvwaddstr($this->infoField, 2, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 3, 2, $wall . ' и ' . $motion);
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Уровень сложности: ' . $complexity);
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Управляйте персонажем стрелками');
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Для старта нажмите "ENTER"');
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для редактирования настроек нажмите "ПРОБЕЛ"');
        $this->refreshField($this->infoField);
    }

    public function editSettingWall()
    {
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите режим стен:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '0 - твердые стены');
        ncurses_mvwaddstr($this->infoField, 5, 11, '1 - проход сквозь стены');
        ncurses_wrefresh($this->infoField);
    }

    public function editSettingMotion()
    {
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите режим движения:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '0 - перемещение по нажатию');
        ncurses_mvwaddstr($this->infoField, 5, 11, '1 - постоянное движение');
        ncurses_wrefresh($this->infoField);
    }

    public function editSettingComplexity()
    {
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 2, 11, 'Выберите уровень сложности:');
        ncurses_mvwaddstr($this->infoField, 4, 11, '1 - легкий');
        ncurses_mvwaddstr($this->infoField, 5, 11, '2 - средний');
        ncurses_mvwaddstr($this->infoField, 6, 11, '3 - трудный');
        ncurses_wrefresh($this->infoField);
    }

    public function drawInfoField($x, $y, $n, $liquid, $constantMotion, $complexity)
    {
        $wall = ($liquid) ? 'прохода сквозь стены' : 'твердых стен';
        $motion = ($constantMotion) ? 'постоянного движения' : 'перемещения по нажатию';
        ncurses_wborder($this->infoField, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($this->infoField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($this->infoField, 1, 2, 'Активен режим:');
        ncurses_mvwaddstr($this->infoField, 2, 2, $wall . ' и ' . $motion);
        ncurses_mvwaddstr($this->infoField, 3, 2, 'Уровень сложности: ' . $complexity);
        ncurses_mvwaddstr($this->infoField, 4, 2, 'Текущая координата x = ' . $x);
        ncurses_mvwaddstr($this->infoField, 5, 2, 'Текущая координата y = ' . $y);
        ncurses_mvwaddstr($this->infoField, 6, 2, 'Количество нажатий клавиш: ' . $n);
        ncurses_mvwaddstr($this->infoField, 7, 2, 'Для выхода из игры нажмите Esc');
        $this->refreshField($this->infoField);
    }

    public function drawField($h, $w, Gamer $gamer, array $characters)
    {
        $paddingTop = (($this->hScreen - $h) + H_INFO_FIELD) / 2;
        $paddingLeft = ($this->wScreen - $w) / 2;
        $this->field = ncurses_newwin($h, $w, $paddingTop, $paddingLeft);
        ncurses_wcolor_set($this->field, 1);
        ncurses_wborder($this->field, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($this->field, $h, $w, '.');
        ncurses_mvwaddstr($this->field, $gamer->getY(), $gamer->getX(), '@');
        foreach ($characters as $character){
            ncurses_mvwaddstr($this->field, $character->getY(), $character->getX(), '*');
        }
        $this->refreshField($this->field);
    }

    public function drawCharacter( $y, $x, $char){
        ncurses_mvwaddstr($this->field, $y, $x, $char);
    }

    private function refreshField ($field){
        ncurses_refresh();
        ncurses_wrefresh($field);
    }

    public function gameOver($h, $w, $liquid, $constantMotion, $complexity)
    {
        $gameOverField = ncurses_newwin(H_INFO_FIELD, W_INFO_FIELD, ($this->hScreen - H_INFO_FIELD) / 2, ($this->wScreen - W_INFO_FIELD) / 2);
        ncurses_wcolor_set($gameOverField, 2);
        ncurses_wborder($gameOverField, 0, 0, 0, 0, 0, 0, 0, 0);
        $this->fillField($gameOverField, H_INFO_FIELD - 1, W_INFO_FIELD - 1, ' ');
        ncurses_mvwaddstr($gameOverField, 3, 19, 'GAME OVER');
        ncurses_mvwaddstr($gameOverField, 6, 5, 'Чтобы сыграть еще раз нажмите "ENTER"');
        ncurses_mvwaddstr($gameOverField, 7, 5, 'Для выхода из игры нажмите "ESC"');
        $this->refreshField($gameOverField);
        while (true) {
            $pressed = ncurses_getch();
            if ($pressed == 13) {
                $this->drawGame($h, $w, $liquid, $constantMotion, $complexity);
                break;
            } elseif ($pressed == EXIT_KEY) {
                break;
            }
        }
    }

    function __destruct() {
        ncurses_end();
    }
}
