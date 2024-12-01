# PDB project 2024 - factory network

## Author:

**Tomáš Vojík** (xvojik00)
- [xvojik00@stud.vutbr.cz](mailto:xvojik00@stud.vutbr.cz)
- [vojik@wboy.cz](mailto:vojik@wboy.cz)

---

## ZIP files

- `README.md`: This file (installation and usage instructions)
- `app.zip`: Application source code &rarr; unzip it and do the installation and setup inside the app
- `navrh.pdf`: Result of project's first part with some modifications based on changes made during development

## Installation

### Configure

Copy the following config files:
- `.env.example` into `.env`
- `config/secret.neon.example` into `config/secret.neon`

Update any configuration in these files if necessary.

### Setup SSL

The application was tested over HTTPS using a self-signed certificate.

By default, NGINX is set up to use the certificate in `docker/nginx/SSL/certs/nginx-selfsigned.crt` and a key in `docker/nginx/SSL/private/nginx-selfsigned.key`.

#### Generating a self-signed certificate

You can generate an SSL certificate using OpenSSL. For example:

```bash
openssl req -x509 -newkey rsa:4096 -keyout docker/nginx/ssl/private/nginx-selfsigned.key -out docker/nginx/ssl/certs/nginx-selfsigned.crt -sha256 -days 365
```

The `docker/nginx/ssl` directory is mapped inside the nginx container. You can change any ssl settings in the `docker/nginx/ssl.conf` file.

#### Using without SSL

If you don't want SSL, deactivate it in the `docker/nginx/nginx.conf` config file.

Comment out (or delete) the following line:

```nginx configuration
include /etc/nginx/ssl.conf;
```

### Setup docker containers

```bash
docker compose up -d
```

### Install DB

> DB installation is automatically run at container startup! It shouldn't be necessary to run manually.

Inside the Roadrunner container run

```bash
bin/console install
```

Or run this in the local environment:

```bash
docker compose exec roadrunner bin/console install
```

### Populate DB with sample data

A sample definition of all entities for a test run is located in the `data.json`. You can populate the app with this sample data by running:

```bash
./setup.sh
```

The script will call the API using `curl`.

> By default, a base URL of `https://localhost` is set inside the `setup.sh` script. You might need to change it.



---

## Usage

The application exposes its HTTP API. Documentation is provided using the OpenAPI Swagger format in the `swagger.yaml` file.

### Testing

The application allows for simulating the whole factory network using a command (inside the `roadrunner` container):

```bash
bin/console simulator:simulate
```

Or locally:

```bash
docker compose exec roadrunner bin/console simulator:simulate
```

For convenience, there is also a helper script that runs the simulation on an infinite loop (every 2s):

```bash
./startSimulation.sh
```

Or locally:

```bash
docker compose exec roadrunner ./startSimulation.sh
```

### Results

Prometheus and Grafana are set up as part of the Docker Compose application. Grafana is accessible on `localhost:3000`.

You can find Grafana dashboard definitions inside the `grafana` directory. You will need to go through the initial setup process for Grafana:

- Configure login
- Add the Prometheus data source using the URL: `http://prometheus:9090`
- Add a MySQL data source using the host URL of `db:3306` and credentials as per the `.env` file

The "Application" dashboard contains all important application metrics about the production and transportation of materials in the factory network.