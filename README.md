# PDB project 

## Author: 

**Tomáš Vojík** (xvojik00) 
- [xvojik00@stud.vutbr.cz](mailto:xvojik00@stud.vutbr.cz) 
- [vojik@wboy.cz](mailto:vojik@wboy.cz)

## Installation

### Configure

Copy the following config files:
   - `.env.example` into `.env`
   - `config/secret.neon.example` into `config/secret.neon`

Update any configuration in these files if necessary.

### Setup docker containers

```bash
docker compose up -d
```

### Install DB

Inside the roadrunner container run

```bash
bin/console install
```

Or run this in local environment

```bash
docker compose exec roadrunner bin/console install
```