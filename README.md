# Importer

A Symfony command that imports a sample feed into a MySQL database.

### Running up the application
- clone the repository
- create the containers `docker compose up -d`
- log into the container `docker exec -it importer-php bash`
- install dependencies `composer install`
- run database migrations `composer db-migrate`
- run tests `composer test`
- import the sample feed `bin/console app:feed_import feed.csv`

### Extensibility
The application is designed with extensibility in mind. By default, it can read from a CSV file and import into a MySQL database.

Support for a different file format can be achieved by adding a reader that implements `FileReaderInterface`. Similarly, support for a different database can be achieved by adding a repository that implements `ProductRepositoryInterface`.

The command can then be run with non-default options: `bin/console app:feed_import feed.xml --format=xml --database=mongodb`

### Logs
The application is set up with Monolog to log in `var/log`.

### Testing
Unit and Integration tests are available and can be run from inside the container with `composer test`
