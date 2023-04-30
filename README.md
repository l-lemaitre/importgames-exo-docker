Instructions for installing the project :<br>
- Run these commands to create the NGINX and PHP-FPM pre-build docker images :<br>
  docker build -t php-fpm-prebuild:latest docker/prebuild/php-fpm<br>
  docker build -t nginx-prebuild:latest docker/prebuild/nginx
- Run this command to create the containers and launch the project :<br>
  docker-compose up -d

The site is accessible at the URL: http://127.0.0.1:8082/importgames/.