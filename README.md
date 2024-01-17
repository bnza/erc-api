### MEDGREENREV API

## Installation

Clone the repository
```shell
git clone https://github.com/bnza/erc-api.git
cd erc-api
```

Clone the ```doctrine-postgis``` repository
```shell
mkdir api/packages/bnza
git clone https://github.com/bnza/doctrine-postgis.git api/packages/bnza
```

Copy root directory ```.env.dist``` to ```.env``` and fill in the correct information.

Generate the ```APP_SECRET``` (with e.g. [coderstoolbox](https://coderstoolbox.online/toolbox/generate-symfony-secret)) and set it in ```api/.env.prod.local```

```shell
APP_SECRET=mysecret
```

Deploy database container 
```shell
docker-compose up database
```

Build and deploy php container
```shell
docker-compose build php
```

Set environment variable in  ```api/.env.prod.local``` 
```
JWT_PASSPHRASE=!ChangeMe!
```

Generate JWT key pairs 
```shell
docker-compose run php bin/console lexik:jwt:generate-keypair
```

Deploy web server container
```shell
docker-compose up nginx
```

