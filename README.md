# DevKinsta CLI

Adds missing functionality and options to DevKinsta application.

Keep in mind that using any of this commands might break your setup. This project is not related with Kinsta in any way,
and there are no guarantees about end-result.

Me, as developer, try to test and foresee any issues and prevent them programatically, but this takes time and
resources.

### During development supported on OSX only

```
Available commands:

container
    container:restart   Restart docker container
    
php
    php:max-file-upload-size    Set post_max_size and upload_max_filesize value for all PHP versions
    php:memory-limit            Set memory_limit value for all PHP versions
    php:set                     Set php ini to given value for all PHP versions
    
sites
    sites:list                  List all sites available in sites.ini
    sites:rebuild               Rebuild sites.ini based on sites in config.json
```
