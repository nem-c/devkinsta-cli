# DevKinsta CLI

Adds missing functionality and options to DevKinsta application.

Keep in mind that using any of this commands might break your setup. This project is not related with Kinsta in any way,
and there are no guarantees about end-result.

Me, as developer, try to test and foresee any issues and prevent them programatically, but this takes time and
resources.

### During development supported on OSX only

### Installation

#### Linux / OSX

```bash
curl -JOL https://github.com/nem-c/devkinsta-cli/raw/trunk/bin/devkinsta-cli.phar
chmod +x devkinsta-cli.phar
mv devkinsta-cli.phar /usr/local/bin/devkinsta-cli
```

### Commands

```
Available commands:

container
    container:restart           Restart docker container
    
php
    php:max-file-upload-size    Set post_max_size and upload_max_filesize value for all PHP versions
    php:memory-limit            Set memory_limit value for all PHP versions
    php:set                     Set php ini to given value for all PHP versions
    
sites
    sites:list                  List all sites available in sites.ini
    sites:rebuild               Rebuild sites.ini based on sites in config.json
```

---

#### container:restart

Restart docker container

```
devkinsta-cli container:restart <name>

Arguments:
    name                    Container name to restart
```

##### Example

```
devkinsta-cli container:restart devkinsta_fpm
```

---

#### php:max-file-upload-size

Set post_max_size and upload_max_filesize values for all PHP versions

```
devkinsta-cli php:max-file-upload-size <value> [--mode]

Arguments:
    value                   New value to use for post_max_size and upload_max_filesize.
                            Should be in [int][M|G] format.
                            
Options:
    --mode=[fpm|cli]        PHP Mode to update PHP setting for [default: "fpm"]
                            Supported "fpm" or "cli"                             
                            
                            
```

##### Example

```
devkinsta-cli php:max-file-upload-size 32M
```

---

#### php:memory-limit

Set memory_limit value for all PHP versions

```
devkinsta-cli php:memory-limit <value>

Arguments:
    value                   New value to use for memory_limit.
                            Should be in [int][M|G] format.

Options:
    --mode=[fpm|cli]        PHP Mode to update PHP setting for [default: "fpm"]
                            Supported "fpm" or "cli" 
```

##### Example

```
devkinsta-cli php:memory-limit 256M
```

---

#### php:set

Set php setting to given value for all PHP versions.

```
devkinsta-cli php:set <variable> <value> [--mode=MODE] [--skip-export=true] [--skip-container-restart=true]

Arguments:
    variable                            Setting value for variable (must include ini group name).
    value                               New value to use for variable.
    
Options:
    --mode=[fpm|cli]                    PHP Mode to update PHP setting for [default: "fpm"]
                                        Supported "fpm" or "cli"
    --skip-export=[bool]                Skip exporting config files before updating [default: false]
                                        Supported "true" or "false"
    --skip-container-restart=[bool]     Skip restarting container after updating value. [default: false]
                                        Supported "true" or "false"
```

##### Example

```
devkinsta-cli php:set PHP.max_input_vars 10000
```

---

#### sites:list

List all sites available in ~/kinsta/sites.ini

```
devkinsta-cli sites:list

Arguments:
    none
    
Options:
    none
```

##### Example
```
devkinsta-cli sites:list
```

---

#### sites:rebuild

Rebuild sites.ini based on sites in config.json

```
devkinsta-cli sites:rebuild

Arguments:
    none
    
Options:
    none
```

##### Example
```
devkinsta-cli sites:rebuild
```

---
