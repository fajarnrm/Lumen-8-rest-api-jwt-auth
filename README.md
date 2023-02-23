# Develop Web Service with Lumen 8.3.1 Use JWT Authentication by Tymon

# Installation

1. Clone this repo or download ZIP

```
git clone https://github.com/fajarnrm/Lumen-8-rest-api-jwt-auth.git
```

2. Install composer packages

```
$ composer install
```

or you can update composer for running this project

```
$ composer update
```

note: in your system mush installed composer

3. Create and setup .env file

```
make a copy of .env.example
$ copy .env.example .env
$ php artisan key:generate

put database credentials in .env file
$ php artisan jwt:secret
```

4. Set your cofigure database first

```
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

5. Migrate database

```
$ php artisan migrate
```

To test application you can use Postman.
Enjoy your project!!
