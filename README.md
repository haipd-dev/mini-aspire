# How to setup and use
1. Clone code to local env
2. Make sure you have docker installed on your environment
3. Run composer install if php8.2 and composer was install on your environment. If not run this command to install by docker.
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```
4. Cd to your project folder copy .env.example file to .env file in the project folder
5. Run sail up to start container
```
./vendor/bin/sail up -d
```
6. Use sail to install project
```
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

```
7. Now the project is ready to run. To create the user run the following command. Remove --is-admin option to create a customer user.
```
./vendor/bin/sail artisan user:create username password --is-admin

```
# Please check the detail document of project here

[Document](https://docs.google.com/document/d/10mcqWKGE-ZKGJPQCgi-UtVisXmG0GeNOBhD8AJD-gjw/edit?usp=sharing).

	
