
# CakePHP 4 Dump SQL plugin

This plugin adds a CakePHP command to easily generate SQL dumps of your configured datasources

Currently the following DBMS are integrated:
- MySQL/MariaDB
- SQLite
- PostgreSQL

## Installation

The recommended way to install this plugin via [composer](https://getcomposer.org) is:

```
composer require lordsimal/cakephp-dump-sql --dev
```

Then execute

```
bin/cake plugin load CakeDumpSql
```

**or** add this to your `src/Application.php` manually

```
public function bootstrap(): void
{
    parent::bootstrap();

    // Other plugins
    $this->addPlugin('CakeDumpSql');
}
```


## Requirements

For each DBMS you need to have its respective dump tool installed.

- MySQL/MariaDB => `mysqldump`
- SQLite => `sqlite3`
- PostgreSQL => `pg_dump`

⚠️ For `pg_dump` it is especially important that you have a compatible version installed. So e.g. if you have a **PostgreSQL 14 server** you need a **pg_dump version 14** ⚠️

## How to use

After installing the plugin you now have a new command available to you:

```
bin/cake dump_sql
```

After executing that command you should see a SQL representation of your `default` datasource inside your console.

So if you want to save it into a file you should do

```
bin/cake dump_sql > dump.sql
```

### Dump different datasource

You can specify which datasource you want to export via the first argument

```
bin/cake dump_sql test > test_dump.sql
```

### GZIP compressed dump

ℹ️ The following feature requires you have the **PHP zlib extension** installed and active ℹ️

```
bin/cake dump_sql --gzip > dump.sql.gz
```

### Data only dump

```
bin/cake dump_sql --data-only > data.sql
```
