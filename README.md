# Тестовое задание

**Дано**:  
Действие происходит на некоторой торговой площадке.

- Заранее созданы юзеры (имеет поле ид),
- к каждому юзеру приписан инвентарь (виртуальный список товаров, где каждый товар имеет поля ид, количество, цена),
- также каждый пользователь имеет приписанный кошелек (хранит поля ид, текущий баланс).

Соответсвенно юзеры, товары и кошельки лежат в своих отдельных таблицах в базе данных (Mysql, если это принципиально, 
организация работы с базой происходит по паттерну ActiveRecord).  

Задача:
1. Реализовать функционал продажи товара от 1 юзера другому, а также сохранение истории покупок. (при условии что цены 
на товары выставляют юзеры и могут менять их в любой момент)
2. Описать что может пойти не так и как можно модифицировать предложенную реализацию (текстом), если продавцом станет 1 
юзер, и будет более 1000 покупок в секунду (считаем что товар в наличии)

Ответы: присылать ввиде ссылки на открытый гитхаб\гитлаб репозиторий, текстовую часть разместить в README.md в корне 
репозитория

### Принятые допущения ###
1. Поскольку задания сделать фронтэнд не было, полагаю что метод продажи товара мы запускаем через API
2. Использую модель юзера, предоставленную фреймворком, вряд ли есть смысл делать отдельную модель
3. Из описания
 > к каждому юзеру приписан инвентарь (виртуальный список товаров, где каждый товар имеет поля ид, количество, цена)

не до конца понятно, нужна связь один-к-одному либо многие-ко-многим. В случае связи один-к-одному, выполнить
продажу товара очень легко, просто поменяв user_id в таблице с товарами, поэтому использую связь многие-ко-многим
и поля "количество", "цена" вынес в промежуточную таблицу. Таким образом пользователи могут продавать друг другу не 
все количество товара, а только какую-то его часть.

4.Не очень понятно, зачем мы храним в списке товаров цену, если при продаже "цены на товары выставляют юзеры и могут 
менять их в любой момент". Учитывая, что в журнале продаж я сохраняю цену сделки, вижу тут небольшую избыточность.
Тем не менее, в списке товаров сохраняется цена, по которой пользователь последний раз купил товар.

5. Процесс реализации "сохранения истории покупок" оставлен на усмотрение исполнителя, поэтому я решил сохранять 
информацию о проведенных сделках в отдельную таблицу "sales_log".

### Ответ на вопрос ###
> Описать что может пойти не так и как можно модифицировать предложенную реализацию (текстом), если продавцом станет 1 
 юзер, и будет более 1000 покупок в секунду (считаем что товар в наличии)

**Возможные проблемы**
- Выполнение 1000 записей в секунду может создать высокую нагрузку на базу данных, что 
может привести к снижению ее производительности или сбою.
- Поскольку продавец один и тот же, высокая частота обновления одних и тех же записей может привести к потенциальным 
взаимным блокировкам на уровне базы данных. 
- Кроме того это может повлиять на целостность данных (к примеру, 
несоответствие количества товара, отсутствие часть данных в журнале, неправильные балансы кошельков).

**Возможные решения**
- Оптимизация запросов к БД, использование индексов
- Распределение нагрузки, вертикальное или горизонтальное масштабирование
- Использование очередей
- Использование транзакций (реализовано в методе sell трейта )
- Использование журналов (запись в журнал производится после успешного завершения транзакции, для этого я реализовал 
интерфейс ShouldDispatchAfterCommit в классе события ProductSold)
- Использование промежуточных кеширующих сервисов (Memcached, Redis) для временного хранения данных и уменьшения 
частоты операций с основной базой данных.

# Установка #

## Скопировать .env файл

```sh
cp .env.example .env
```

## Установка пакетов

```sh
composer install
```

## Запуск контейнеров

```sh
./vendor/bin/sail up -d
```

## Запуск миграций

```sh
./vendor/bin/sail artisan migrate --seed
```

## Запрос к API

> Продать товар id=7 пользователя id=2 пользователю id=1 по цене 11.47 (количество 10)

> ❗В связи с тем, что БД наполняется тестовыми данными случайным образом, может потребоваться поменять
некоторые id

```sh
curl --location 'http://localhost/api/products/7/sell' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'from_user=2' \
--data-urlencode 'to_user=1' \
--data-urlencode 'price=11.47' \
--data-urlencode 'quantity=10'
```

### Остановка контейнеров

```sh
./vendor/bin/sail down
```
