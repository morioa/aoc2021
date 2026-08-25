# install base image
FROM php:7.4-alpine

# set app user and related directories
#ARG uid=1000
#ARG user=cisuser
#RUN useradd -G app -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user

# install supporting packages
RUN apk add \
    git \
    curl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# set env vars
WORKDIR /app
#USER $user

CMD [ "php", "background/run.php" ]
