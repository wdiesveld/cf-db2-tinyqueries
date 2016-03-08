## CloudFoundry PHP setup for TinyQueries on DB2

This is an application which can be used to setup TinyQueries on CloudFoundry using the [PHP DB2 Build Pack].

It is an out-of-the-box implementation of [TinyQueries PHP-libs v3.0.7] and is meant to be used together with the TinyQueries compile service to build a REST-api by only defining queries.

### Usage

1. Clone the app (i.e. this repo).

  ```bash
  git clone https://github.com/wdiesveld/cf-db2-tinyqueries
  cd cf-db2-tinyqueries
  ```

1. If you don't have one already, create a DB2 database service. 

1. If you don't have one already, create a TinyQueries project. With Pivotal Web Services, the following command will create a free TinyQueries project through [TinyQueries].

  ```bash
  cf create-service tinyqueries free my-test-tinyqueries-project
  ```

1. Edit the manifest.yml file.  Change the 'host' attribute to something unique. Then under "services:" change "my-test-db2" to the name of your SQL database service. This is the name of the service that will be bound to your application and thus available to this application. Do the same for "my-test-tinyqueries-project"

1. Push it to CloudFoundry.

  ```bash
  cf push
  ```

1. After the application is deployed, you need to run the init-config script in your browser.

	```bash
	[your-application-url]/init-config.php
	```
	
1. After the application is deployed you can access your application URL in the browser. You will find further instructions how to use TinyQueries from there.	

[TinyQueries PHP-libs v3.0.7]:https://github.com/wdiesveld/tiny-queries-php-api/releases/tag/v3.0.7a
[TinyQueries]:http://www.tinyqueries.com
[PHP DB2 Build Pack]:https://github.com/ibmdb/db2heroku-buildpack-php


