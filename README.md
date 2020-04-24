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

Git branches:
* Create new branch ```git checkout -b name```
* Push git branch ```git push origin head```