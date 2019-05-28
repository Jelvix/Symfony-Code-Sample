### Web API Example with OAuth2 and JWT authorization

This project was created in order to show programming skills of PHP engineers from Jelvix.


1) Setup and run composer

```bash
cd Symfony-Code-Sample 
composer install
```

2) Setup connection strings for Mysql and Redis in `.env` file (or `.env.local` or `.env.(dev|prod|staging)` )
```dotenv
DATABASE_URL=mysql://user:pass@127.0.0.1:3306/test_db
REDIS_URL=redis://localhost:6379
```

3) Run doctrine migrations
```bash
bin/console doctrine:migrations:migrate
```

4) Generate public/private keys pair for JWT authorization tokens signing
```bash
openssl genpkey -algorithm RSA -out ./cfg/jwt_private_key.pem -pkeyopt rsa_keygen_bits:2048

#extract public key
openssl rsa -pubout -in ./cfg/jwt_private_key.pem -out ./cfg/jwt_public_key.pem
```

5) Optional: load dummy data to DB for test or development purposes using fixtures
```bash
bin/console doctrine:fixtures:load
```

6) Run dev server
```bash
bin/console server:run
```

To see full list of additional supported commands run:
```bash
bin/console
```

### Generate new entity and create migration

```bash
# create new entity
bin/console make:entity 

# generate new migration
bin/console make:migration 

# apply migration
bin/console doctrine:migrations:migrate 

```

### Testing

Run tests
```bash
bin/phpunit
```

### Webpanel deployment

1) Install all the webpanel dependencies
```bash
cd webpanel
npm install
```

2) run dev server
```bash
npm start
```
or
```bash
ng serve
```

3) build code
```bash
npm build
```
or
```bash
ng build
```

### Production build

To build for production run symfony command
```bash
bin/console app:webpanel:build --prod=1
```
Built files will be placed to configuration file `config/packages/webpanel.yaml`.
After build process is finished, webpanel will be available on default Symfony route `http://domain_name/`

### See all available API
To see and test all the available API's go to SwaggerUI docs page `http://localhost:8000/api/doc` 


**Note: FOR CODE DEMONSTRATION ONLY.**