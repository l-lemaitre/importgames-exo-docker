FROM nginx:latest

WORKDIR /app

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . .

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "vim"]
RUN ["apt-get", "install", "-y", "nano"]
