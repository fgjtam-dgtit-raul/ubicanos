<p align="center" style="font-size: 2rem;">UBÍCANOS</p>


## Setup

- Create `.env` file

`cp .env.example .env`

- Runs the migrations to create the tables in the database

`php artisan migrate`

- Initialized the municipalities catalog

`php artisan db:seed --class=MunicipalitySeeder`

- Install composer dependencies

`composer install`

- Install JavaScript dependencies

`npm install`

- Build JS assets (Production environment)

`npm run build`

### Configure the corresponding variables in the `.env` file.

```bash
AUTH_API_ENDPOINT="http://test-server:8080"
AUTH_API_TOKEN=

DB_MONGO_HOST=mongo
DB_MONGO_PORT=27017
DB_MONGO_DATABASE=local
DB_MONGO_USERNAME=root
DB_MONGO_PASSWORD=example
DB_AUTHENTICATION_DATABASE=
```


### docker-compose.override.yml
```yml
services:
  mongo:
    image: mongo
    restart: always
    networks:
      - ubicanos
    environment:
      MONGO_INITDB_ROOT_USERNAME: root
      MONGO_INITDB_ROOT_PASSWORD: example
    volumes:
      - ./database/mongodb/:/data/db

  mongo-express:
    image: mongo-express
    restart: always
    networks:
      - ubicanos
    ports:
      - 8081:8081
    environment:
      ME_CONFIG_MONGODB_ADMINUSERNAME: root
      ME_CONFIG_MONGODB_ADMINPASSWORD: example
      ME_CONFIG_MONGODB_URL: mongodb://root:example@mongo:27017/
      ME_CONFIG_BASICAUTH: false

volumes:
  mysql:
    driver: local
```