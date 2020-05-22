# LCWO - Learn CW Online Code Repository

This is the source repository for *Learn CW Online*, a website to learn and
practice Morse code in the browser. The official website of LCWO is
https://lcwo.net/. The code can be found at
https://git.fkurz.net/dj1yfk/lcwo

The code is licensed under the GNU AGPL 3. 

## Running LCWO

The fastest way to get a running instance of LCWO is by using Docker.
There's a shell script called `docker_start.sh` which builds
and runs the Docker image and fires up an instance of LCWO which you can reach
at `http://localhost:8000`. There's an user `admin` with the password `admin`.

On Ubuntu, install Docker and add your user to the docker group first;
```
sudo apt-get install docker.io    # unless you already installed docker
sudo usermod -a -G docker $USER   # you must log out and to make this work
```

Then simply clone the repository and run the container:
```
git clone https://git.fkurz.net/dj1yfk/lcwo.git
cd lcwo
./docker_build.sh
./docker_start.sh
```

Note that this is mainly meant for development and testing purposes, to give
you a fully working LCWO instance from scratch without any effort. For a
deployment as a public website this may not be what you want.

## Configuration

Most configuration options are set in `inc/definitions.php`. A template is
included in the official sources, which is also used for the Docker image. For
your own instance.

If you want to change the configuration you could change this file directly,
but the *clean* way of doing this is by copying this file to
`inc/definitions.custom.php` which overrides the default file if present.

## Database

The database table schemata and contents needed to run LCWO are located in the
directory `db`. There are the following tables:

* ...
* ...

## Architecture

## How to read the code

## TODOs

### Code Quality

The code quality of LCWO varies strongly between the different parts, from
terrible spaghetti code to somewhat structured and modular. Refactoring needed.

