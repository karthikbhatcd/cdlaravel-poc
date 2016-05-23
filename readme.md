# Laravel API POC

POC API application on Laraval for Opportunities Resource.


## Official Laravel Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Folder Structure
```sh
app
	all models go here
	-- http
		-- Controllers
			all controllers go here
		-- Helpers
			helper function files
		-- Middleware
			middleware classes
		routes -- url router
config
	application configuration files
	app.php
		service providers, namespace aliases, drupal token variables
	database.php
		database connection setup
public
	the directory visible to public - images, css etc
resources
	raw assets like sass, php / blade templates etc
storage
	contains compiled files, session files, cached files etc
tests
	directory contains your test files
vendor
	contains Composer dependencies
```

## Installation
 - Install composer : http://getcomposer.org
 - Install laravel :
	`composer global require "laravel/installer=~1.1"`
 - Install mongodb : https://docs.mongodb.com/v3.2/installation/
 - cd to a directory of choice where application is to installed
 - `git clone https://github.com/karthikbhatcd/cdlaravel-poc.git`
 - `cd cdlaravel-poc`
 - copy .env.example to .env
 - Enter database details, app details in .env file
 - `php artisan key:generate`
 - `composer update`

# Routes

 - Get all Opportunities
   - GET `/opportunities`
 - Get single Opportunity
   - GET `/opportunities/{opportunity_id}`
 - Create an Opportunity
   - POST `/opportunities`
   - data:
   - ```sh
		{
			"title": "Opportunity Title",
			"logo": "http://logo_url.jpg"
		}
 - Update single Opportunity
   - PUT `/opportunities/{opportunity_id}`
   - data:
   - ```sh
		{
			"title": "Opportunity Edited Title",
			"logo": "http://logo_url.jpg"
		}
 - Delete single Opportunity
   - DELETE `/opportunities/{opportunity_id}`