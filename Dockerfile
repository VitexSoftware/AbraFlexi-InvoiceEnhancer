FROM debian:latest
MAINTAINER Vítězslav Dvořák <info@vitexsoftware.cz>
ENV DEBIAN_FRONTEND=noninteractive
ENV PHPVER=8.2

RUN apt update
RUN apt-get update && apt-get install -my wget gnupg lsb-release

RUN echo "deb http://repo.vitexsoftware.com $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/vitexsoftware.list
RUN wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.com/keyring.gpg
RUN apt update
RUN apt-get -y upgrade
RUN apt -y install apache2 libapache2-mod-php php-pear php-curl php-mbstring curl composer php-intl locales-all unzip ssmtp 
RUN apt -y install abraflexi-enhancer
RUN a2dissite 000-default
RUN a2enconf abraflexi-enhancer

RUN echo "<?php header('Location: /abraflexi-enhancer/');" > /var/www/html/index.php ; rm /var/www/html/index.html

#RUN sudo cp /usr/lib/php/${PHPVER}/php.ini-development /etc/php/${PHPVER}/apache2/php.ini
    
COPY debian/conf/mail.ini   /etc/php/${PHPVER}/conf.d/mail.ini
COPY debian/conf/ssmtp.conf /etc/ssmtp/ssmtp.conf

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV EASE_LOGGER syslog|console

RUN echo 1 > /tmp/1

EXPOSE 80
CMD ["/usr/sbin/apachectl","-DFOREGROUND"]
