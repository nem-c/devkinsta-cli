# DevKinsta CLI

Adds missing functionality and options to DevKinsta application.

### During development supported on OSX only

Commands groups and options:

#### Container

`container:restart [name]` restart container name by docker name

#### Kinsta

`kinsta:rebuild-sites-ini` recreates /kinsta/sites.ini file in case it gets damaged

#### PHP

`php:memory-limit` changes memory limit on php-fpm for all PHP versions
