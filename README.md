# Hive backend

## Installation
- run `git clone ...` to pull the source code
- create `.env.local` and configure DB credentials
- run `composer install` to install 3rd party dependencies
- run `php bin/console doctrine:database:create` to create DB
- run `php bin/console doctrine:migrations:migrate` to create DB schema
- you can run `mysql hive_db_name < src/Resources/sample_data.sql` to import sample data
