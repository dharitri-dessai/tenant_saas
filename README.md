# tenant_saas
Tenant Saas

# Install symfony packages
composer install

# Install node packages
npm install

# Create a empty db and run migrations
php bin/console doctrine:migration:migrate

# create super admin using the following command
php bin/console app:create-super-admin

# Start the server
symfony server:start

# Go To login url and login super admin password
Create tenants and subscriptions 

OR

Go to register and register tenannt or user
