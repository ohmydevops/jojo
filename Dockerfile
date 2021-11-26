FROM php:8.0-cli-alpine

RUN docker-php-ext-install sockets

#ENV BASE_WEB_DIR "/jojo/"

COPY main.php /jojo/jojo-server
COPY 404.html /jojo/404.html
RUN chmod +x /jojo/jojo-server

EXPOSE 8000

ENTRYPOINT ["php", "/jojo/jojo-server"]
