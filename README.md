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

### Boolean

Generate a random boolean

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/boolean.png"  width="450">

### Int

Generate a random integer

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/int.png"  width="450">

### Float

Generate a random number with decimals

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/float.png"  width="450">

### OneOn

Return true with a probability of 1 on X

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/one-on.png"  width="450">

### String

Generate a random string

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/string.png"  width="450">

### StringInt

Generate a random integer into a string

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/string-int.png"  width="450">

### FirstName

Generate a first name

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/first-name.png"  width="450">

### LastName

Generate a last name

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/last-name.png"  width="450">

### Company

Generate a random company name

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/company.png"  width="450">

### Ipv4

Generate a random ipv4 address

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/ipv4.png"  width="450">

### Ipv6

Generate a random ipv6 address

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/ipv6.png"  width="450">

### Word

Generate a random fake word

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/word.png"  width="450">

### Text

Generate a random text with X words

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/text.png"  width="450">

### ChooseValueFrom

Choose randomly one or multiple value from a given array.

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/choose-value-from.png"  width="450">

### ChooseKeyFrom

Choose randomly one or multiple key from a given array.

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/choose-key-from.png"  width="450">

### ChooseBothFrom

Choose randomly one or multiple key-value from a given array.

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/choose-both-from.png"  width="450">

### Array

Generate an array with the given keys and the given parameters for the values associated. You can precise the lenght wanted for the array (default 3).

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/array.png"  width="450">

### Json

Generate a json with the given keys and the given parameters for the values associated.

Example:

<img src="https://github.com/BatMaxou/aatis-fixtures/blob/feat/readme/docs/images/json.png"  width="450">

## Config file
