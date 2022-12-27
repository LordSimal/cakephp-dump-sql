
# CakePHP 4 Dump SQL plugin

This plugin adds a CakePHP command to easily generate SQL dumps of your configured datasources

Currently only MySQL/MariaDB exports are supported but PostgreSQL and SQLite shouldn't be that hard to integrate along the way.

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