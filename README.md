# JSON API code sample.

Able to:
* Retrieve movie data given a unique ID
* Submit movie rating per user
* Retrieve overall movie rating based on all users' ratings

Implements the following [JSON API](http://jsonapi.org/format/) endpoints:
* /movies/{movie_id}
* /movieratings/{movie_id}
* /usermovieratings/{user_id}/movies/{movie_id}

Note that per the JSON API specification, all requests must contain the `Content-Type: application/vnd.api+json` header 

To test:
1. Ensure you have PHP >7.0 installed and MySQL server >5.7 running (eg: `mysql.server start`)
1. Clone the contents of this respository to the test environment
1. Install the Slim framework and PHPUnit dependencies with [Composer](https://getcomposer.org)
   ```
   > cd slim-json-api
   > php composer.phar install
   ```
4. Update MySQL connection settings in `/config/default.php` if necessary
5. Install the testing database
   ```
   > php app.php install stage
   ```
6. Start PHP's built-in application server
   ```
   > php -S 127.0.0.1:8080 -t public
   ```
7. Open a new terminal session in the project folder and run the test suite
   ```
   > cd slim-json-api
   > php vendor/bin/phpunit
   ```
