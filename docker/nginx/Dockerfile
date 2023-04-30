FROM nginx-prebuild:latest

WORKDIR /app

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . .
