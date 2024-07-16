# Тестовое задание

Дано:
Действие происходит на некоторой торговой площадке.

-Заранее созданы юзеры (имеет поле ид),
-к каждому юзеру приписан инвентарь (виртуальный список товаров, где каждый товар имеет поля ид, количество, цена),
-также каждый пользователь имеет приписанный кошелек (хранит поля ид, текущий баланс).

Соответсвенно юзеры, товары и кошельки лежат в своих отдельных таблицах в базе данных (Mysql, если это принципиально, организация работы с базой происходит по паттерну ActiveRecord).
Задача:

1. Реализовать функционал продажи товара от 1 юзера другому, а также сохранение истории покупок. (при условии что цены на товары выставляют юзеры и могут менять их в любой момент)
2. Описать что может пойти не так и как можно модифицировать предложенную реализацию (текстом), если продавцом станет 1 юзер, и будет более 1000 покупок в секунду (считаем что товар в наличии)
   Ответы:
   присылать ввиде ссылки на открытый гитхаб\гитлаб репозиторий, текстовую часть разместить в README.md в корне репозитория

**_Установка_**

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

## Запросы к API

### остановка контейнеров

```sh
./vendor/bin/sail down
```
