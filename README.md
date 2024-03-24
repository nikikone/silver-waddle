## Описание и цель 

В большом помещении (например, торговый центр) возле каждого входа и выхода есть устройство, которое считает и отдает 
информацию о количестве входящих и выходящих людей. Данное устройство интегрировано с нашим проектом и умеет передавать 
нам эти данные.

**Цель** - сделать вывод для клиентов информации о загрузке помещения

Пример для понимания - 
https://salsknews.ru/wp-content/uploads/2017/08/oooza969d01padjyjkt7bfsupkyor2i1lmc0fa6v.jpg
https://www.bibliotika.ru/system/images/412_medium.jpg

## Было сделано
- Миграции
- Логика трёх эндпоинтов (один на добавление информации, два других - на получение).
- Консольная команда, очищающая таблицу `minutes`, сохраняющая рассчёты в таблицы `days` и `hours`. Команда должна выполняться когда внесения изменений в информацию о прошедших днях уже не будет.
- Команда поставлена на ежедневное выполнение в полночь.
- Все написанные классы вынесены в отдельную папку Classes в app.
















