FROM php:8.0-cli-alpine

RUN docker-php-ext-install sockets

ENV BASE_WEB_DIR "/jojo/"

COPY main.php /jojo-server
RUN chmod +x /jojo-server

EXPOSE 8000

ENTRYPOINT ["php", "/jojo-server"]
