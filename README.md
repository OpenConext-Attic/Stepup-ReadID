Stepup-ReadID
===================

![test-integration](https://github.com/OpenConext/Stepup-ReadID/workflows/test-integration/badge.svg)

IdP for ReadId integration 

Development environment
======================

The purpose of the development environment is mainly for running the different test and metric tools. But can also be
used with the built in web server to develop the application. Alternatively the Symfony / PHP built in webserver can be 
used.

Requirements
-------------------
An operating system of your choice which can run:
- Docker (tested with v18.03.1)
- Docker-compose (tested with v1.18.0)

All development could be done in-container. If you opt to run this application on bare metal. Additional requirements are

- PHP 7.2
- Composer

Install
-------------------
**Create a .env file**
1. `$ cp .env.dist .env`
1. Edit the `.env` file with the editor of your choice and: 
    1. Update the `APP_SECRET` to a value of your liking. See [Symfony docs](https://symfony.com/doc/current/reference/configuration/framework.html#secret) for more details about this secret. 
    1. Set the `APP_ENV` to 'dev'

**Copy the parameters.yaml**

`$ cp config/packages/parameters.yaml.dist config/packages/parameters.yaml`

**Bring up Docker**

```
$ ./support/docker/init.sh
```

If everything goes as intended, you can develop in the virtual machine.

The machine should be available at: [127.43.33.34:443](https://127.43.33.34:443). Feel free to add a `hosts` file entry,
I used: 

[readid.stepup.example.com](https://readid.stepup.example.com)

Tools
------
In order to make development in the container slightly easier two helper scripts where added to the project.

You can use:

`./support/docker/bash.sh` to open an interactive shell session (ctrl+d to exit out of this session).

`./support/docker/composer.sh` to run composer tasks in box.

Example usage:

```
 $ pwd
 > /home/user/projects/Stepup-ReadId
 $ ./support/docker/composer.sh require --dev ibuildings/qa-pack
 > Composer output..
 $ ./support/docker/bash.sh
 user@8e5bfa7073a1:/var/www $ pwd
 > /var/www
 user@8e5bfa7073a1:/var/www $ ctrl+d
 exit
 $ ...
```  

Debugging
-------------------
Xdebug is available. It's configured with auto connect IDE_KEY=phpstorm. 

Tests and metrics
==================

To run all required test you can run the following commands from the dev env:

```bash 
    composer check
```

Every part can be run separately. Check "scripts" section of the composer.json file for the different options.

GitHub Actions are used as CI environment. The `composer check` is performed and should pass in order to get a 'green' build.

Release instructions
=====================

Please read: https://github.com/OpenConext/Stepup-Deploy/wiki/Release-Management for more information on the release strategy used in Stepup projects.

Other resources
======================

 - [Developer documentation](docs/index.md)
 - [Issue tracker](https://www.pivotaltracker.com/n/projects/1163646)
 - [License](LICENSE)
