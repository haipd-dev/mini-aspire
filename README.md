# How to setup and use
1. Clone code to local env
2. Make sure you have docker installed on your environment
3. Cd to your project folder copy .env.example file to .env file in the project folder
4. Run sail up to start container
```
./vendor/bin/sail up -d
```
5. Use sail to install project
```
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

```
6, Now the project is ready to run. To create the user run the following command. Remove --is-admin option to create a customer user.
```
./vendor/bin/sail artisan user:create username password --is-admin

```
# Please check the detail document of project here

[Document](https://docs.google.com/document/d/10mcqWKGE-ZKGJPQCgi-UtVisXmG0GeNOBhD8AJD-gjw/edit?usp=sharing).

	
