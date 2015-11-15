# gitHubWatcher
- lets you check user's public repositories
- logs search entries
- deletes entries older than a given number of hours
- sample user -> admin-admin

# Requirements

- MySQL 5.5 or later
- PHP 5.3.4 or higher
- The app is based on FatFree Framework (F3) http://fatfreeframework.com/
- Composer

# How to install

- clone repository

- set up virtual host

- create database

- rename config/config.ini.default to config.ini

    - change database details
    - add your security token from GitHub, see more info on https://help.github.com/articles/creating-an-access-token-for-command-line-use/
    
- composer install

 You are ready to party! :D
 
 # FAQ
 
* I want to see Mr. Bean !
    * Then provide an incorrect access_token in config.ini and try to search for someone
 
 
* I want to see Panda ! :)
    * Suure, try to go to a page that doesn't exist
 