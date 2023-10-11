# Aatis-fixtures

## Installation

with composer

```
composer require aatis/fixtures
```

**Don't forget to give the DATABASE_URL in your .env/.env.local file**

## Commands

### Refresh database

The **--force (-f)** flag is required to drop and recreate the database

- **All**

Drop and recreate database with it schema

```
php bin/console aatis:database:refresh -f
```

- **Only for chosen tables**

Truncate chosen tables

```
php bin/console aatis:database:refresh table1,table2 -f
```

**Be aware of the entity references**

### Generate models

Generate your entity models for the database into the ***./config/fixtures/config.yaml*** file

```
php bin/console aatis:model:generate
```

### Generate fixtures

Generate fixtures with **Aatis Faker** base on the config of the ***./config/fixtures/config.yaml*** file into this same file

```
php bin/console aatis:fixtures:generate
```

### Load fixtures

The **--force (-f)** flag is required to drop and recreate the database

- **All**

Refresh the datebase and load the data from the ***./config/fixtures/config.yaml*** file

```
php bin/console aatis:load:fixtures -f
```

- **Only for chosen tables**

Refresh the datebase and load the data of chosen tables base on the ***./config/fixtures/config.yaml*** file

```
php bin/console aatis:load:fixtures table1,table2 -f
```

**Be aware of the entity references**

### Faker List

List all the methods available for the faker with there presentation

```
php bin/console aatis:faker:list
```

## Faker

## Config file
