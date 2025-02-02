FROM php:8.0-cli-alpine

RUN docker-php-ext-install sockets

ENV BASE_WEB_DIR "/joojoo/"

COPY main.php /joojoo
RUN chmod +x /joojoo

EXPOSE 8000

ENTRYPOINT ["php", "/joojoo"]
