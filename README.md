## Don't have time?
If you don't have time to test the project you can see a demo here
https://bit.ly/git-assistant

Otherwise, you can follow the README to make it work on your host.

----

# Project installation guide

The project is dockerized and Makefile commands are used to facilitate the installation process.

Before proceeding, make sure you have docker, docker-compose and Make.


## Cloning project

    git clone git@github.com:ADS-SANDBOX/ai-toolbox.git

## Configuring ports
The file https://github.com/ADS-SANDBOX/ai-toolbox/blob/main/.docker/.env.example contains the name of the containers and the port to be used by default.

This port will be placed in the second position of each container mapping.

If you do not change it, you can continue with the guide, if you change it, keep in mind that you will have to take it into account when opening the project in the web browser.

It is important to verify that there are no conflicts with other containers that we may have running on our host.

## Start project
Once we have cloned the project and verified that we can use the port defined in the .env.example, we will execute these commands

```bash 
make docker-env
```

```bash 
make build
```

```bash 
make up
```

```bash 
make init
```

> The `make init` will take care of the composer install, migrations and everything necessary to test the API.

## Testing project
If we have not changed the port, we will be able to access `Scribe` through this url http://127.0.0.1:830/docs/

If you have changed the port modify the URL, if something does not work make sure that all containers are up and no port conflict has occurred.

## Automated tests
The project has some automatic tests that you can also run with Make commands.

```bash 
make feautre-test
```

```bash 
make integration-test
```

```bash 
make unit-test
```

If you prefer, you can launch all of them with the command `make test`.

----

## More info
For more information you can read this Notion https://bit.ly/git-assistant-notion
