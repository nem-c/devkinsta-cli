# DevKinsta CLI

Adds missing functionality and options to DevKinsta application.

Keep in mind that using any of this commands might break your setup. This project is not related with Kinsta in any way,
and there are no guarantees about end-result.

Me, as developer, try to test and foresee any issues and prevent them programatically, but this takes time and
resources.

### During development supported on OSX only

Commands groups and options:

#### Container

`container:restart [name]` restart container name by docker name

#### Kinsta

`sites:rebuild` recreates /kinsta/sites.ini file in case it gets damaged

`sites:list` list all sites available in sites.ini

#### PHP

`php:memory-limit` changes memory limit on php-fpm for all PHP versions
