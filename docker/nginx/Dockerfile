FROM nginx:mainline-alpine

WORKDIR /app

RUN apk update && apk upgrade --no-cache

# Text editors
RUN apk add --no-cache vim nano

COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY . .
