=================================
BRANCH: task-03  -  "Game"
=================================

Task:

В  консоли рисуется поле m x n. Поле ограниченно рамкой. Каждая точка поля обозначается например точкой ( легко должно меняться ).
На поле  находится отображение персонажа - зеленый символ @. Символ можно перемещать с помощью стрелок клавиатуры. Предусмотреть два режима - перемещение только при нажатии стрелки и постоянное перемещение, а при нажатии стрелок - смена направления.
При достижении границы поля  - возможны два режима - появление с другой стороны поля и остановка.
Над или под полем есть информационная область, которая отображает текущие координаты персонажа и число нажатий на кливиши, которое было сделано с момента запуска программы.
Добавить в игру новых персонажей. Их будет два типа:
1) двигается случайно
2) Если персонаж игрока ближе чем на 5 клеток к персонажу - персонаж начинает преследовать ( двигаться в сторону ) игрока
Каждый тип отображается новым цветом и новым символом.
На один ход игрока ( передвижение на одну клетку ) - в любом режиме - делается ход каждого персонажа
Если персонажи оказываются в одной клетке с игроком - игрок погибает. Игра останавливается.
В конце игры запросить имя пользователя и сохранить его и число шагов которые он сделал до проигрыша/конца игры.
Статистику нужно показать в виде таблицы при запуске игры ( как заставку ) и в конце после ввода имени пользователя.
Данные нужно сохранять в базе SQLite. Файл базы должен лежать в папке с самой игрой. Нормально должно обрабатываться отсутсвие файла (он должен создаваться заново).

Solution:

https://github.com/LisKorzun/PHP---Training_Tasks/tree/task-03/Task_03

Video:

[![ScreenShot](https://github.com/LisKorzun/PHP---Training_Tasks/blob/task-03/Task_03/img/game_php_ncurses.png)](https://youtu.be/Utnoq-KWFWc)