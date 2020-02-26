Stepup-ReadID
===================

<a href="#">
    <img src="https://travis-ci.org/OpenConext/Stepup-ReadID.svg?branch=develop" alt="build:">
</a></br>

IdP for ReadId integration 

Development environment
======================

The purpose of the development environment is mainly for running the different test and metric tools. But can also be
used with the built in webserver to develop the application. Alternatively the Symfony / PHP built in webserver can be 
used.

Requirements
-------------------
(TODO expand requirements)
- Docker 
- Composer

Install
-------------------
**Create a .env file**
(TODO verify existing steps, rewrite where required)
1. `$ cp .env.dist .env`
1. Edit the `.env` file with the editor of your choice and: 
    1. Update the `APP_SECRET` to a value of your liking. See [Symfony docs](https://symfony.com/doc/current/reference/configuration/framework.html#secret) for more details about this secret. 
    1. Set the `APP_ENV` to 'dev'

**Copy the parameters.yaml**

`$ cp config/packages/parameters.yaml.dist config/packages/parameters.yaml`

**Bring up Docker**

```

```

If everything goes as intended, you can develop in the virtual machine.

[https://readid.stepup.example.com](https://readid.stepup.example.com)

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

Release instructions
=====================

Please read: https://github.com/OpenConext/Stepup-Deploy/wiki/Release-Management for more information on the release strategy used in Stepup projects.

Other resources
======================

 - [Developer documentation](docs/index.md)
 - [Issue tracker](https://www.pivotaltracker.com/n/projects/1163646)
 - [License](LICENSE)
