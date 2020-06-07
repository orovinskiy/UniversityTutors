# UniversityTutors

Running development environment on local with Docker
* Install docker desktop and make sure it is running on your computer
* From the project root, run docker-compose up
* The default url is localhost:5001/
* To run composer for the first time on your docker server, use ```docker exec -it appname bash``` and run ```composer install```

To see the currently running containers
* Run ```docker ps```

To update composer
* ```docker exec -it appname bash``` and then run ```composer update```

To enable routing for the docker container
* Make sure the .htaccess file is in the project root
* Run the command ```docker exec -it app_name bash``` to open a terminal inside the running docker app container
* Run the command ```a2enmod rewrite``` and then ```service apache2 restart```. This will terminate the web_app container so you'll need to restart it with ```docker-compose up```

If adding files in not allowed
* Permission for uploads folder on server ```chmod 777 uploads```

If mkdir permission denied on load
* ```chmod 777 html``` on the whole /var/www/html folder

If error ```php_network_getaddresses: getaddrinfo failed: Temporary failure in name resolution```
* Database configurations are wrong, change to mysql host to 'localhost'

### Git branches:
* Create new branch ```git checkout -b name```
* Push git branch ```git push origin head```
* Get current branch ```git rev-parse --abbrev-ref HEAD```

### Deployment
If routing isn't working 
 - Go into apache config file ``` /etc/apache2/apache2.conf ```
 - Make sure overwrite is on If doesnt work change it to all the files `<Directory /var/www/> AllowOverride All </Directory>`
                                                                           
                                                                                 
                                                                           
