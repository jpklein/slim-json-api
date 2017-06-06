# REST API code sample.

Able to:
 * Retrieve movie data given a unique ID
 * Submit movie rating per user
 * Retrieve overall movie rating based on all users' ratings

To test:
1. Ensure you have PHP >7.0 installed and MySQL server >5.7 running (eg: `mysql.server start`)
1. Clone the contents of this respository to the test environment
1. Install Slim package with [Composer](https://getcomposer.org)
   ```
   > cd rest-sample
   > php composer.phar install
   ```
4. Install the testing database
   ```
   > php app.php install stage
   ```
5. Start PHP's built-in application server
   ```
   > php -S 127.0.0.1:8080 -t public
   ```
6. Open a browser to test the movie endpoint: `http://127.0.0.1:8080/movies/1`
7. Open a browser to test the movierating endpoint: `http://127.0.0.1:8080/movieratings/1`
