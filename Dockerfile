FROM phusion/baseimage:0.11
MAINTAINER Fabian Kurz <fabian@fkurz.net>

CMD ["/sbin/my_init"]

RUN apt-get update && apt-get install -y apache2 mysql-server php \
    libapache2-mod-php php-mysql php-gd exim4 php-mbstring \
    # stuff needed to compile ebook2cw \
    build-essential libmp3lame-dev git libvorbis-dev \
    # stuff to work a little more comfortably within the container
    vim

RUN a2enmod rewrite cgi

# We want to send mails directly to any destination with exim4
# WARNING: This is not exactly what you probably want in a productive environment.
RUN sed -i "s/dc_eximconfig_configtype='local'/dc_eximconfig_configtype='internet'/g" /etc/exim4/update-exim4.conf.conf

# We want short open tags for PHP like <? ?>
RUN sed -i 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/*/apache2/php.ini

# copy PHP stuff
COPY . /www/
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf

# change HOSTNAME to localhost:8000
RUN sed -i 's/define("HOSTNAME",  "lcwo.net");/define("HOSTNAME",  "localhost:8000");/g' /www/inc/definitions.php

# change session store location
RUN sed -i 's/php_value session.save_path/#/g' /www/.htaccess

# create image dir
RUN mkdir /www/img/
RUN chmod a+rwx /www/img/

# install ebook2cw in CGI mode (utf8)
RUN mkdir -p /tmp/ebook2cw_build && \
    cd /tmp/ebook2cw_build && \
    git clone https://git.fkurz.net/dj1yfk/ebook2cw && \
    cd ebook2cw && \
    sed -i "s/cw->encoding = ISO8859;/cw->encoding = UTF8;/g" ebook2cw.c && \
    make cgi && \
    mkdir -p /www/cgi-bin/ && \
    cp cw.cgi /www/cgi-bin/cw.mp3 && \
    cp cw.cgi /www/cgi-bin/cw2.mp3 && \
    make cgi USE_LAME=NO && \
    cp cw.cgi /www/cgi-bin/cw.ogg && \
    cp cw.cgi /www/cgi-bin/cw2.ogg 

COPY config/.vimrc /root

# Apache will log into this directory, conveniently available via web (protect
# this in production!)
RUN mkdir /www/web-log/

# Create a database and tables within the container 
RUN service mysql start && \
    mysqladmin -u root create LCWO && \
    echo "GRANT ALL ON LCWO.* TO lcwo@localhost IDENTIFIED BY 'lcwo'; FLUSH PRIVILEGES;" | mysql -uroot && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_schema.sql && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_texts.sql && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_users.sql && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_plaintext.sql && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_words.sql && \
    mysql -ulcwo -plcwo LCWO < /www/db/lcwo_config.sql && \
    service mysql stop 

# note that the pre-populated database now resides in /var/lib/mysql
# later we can run the container and mount a volume to
# /var/lib/mysql. If this volume doesn't exist, it will
# be created automatically and populated with the existing
# directory we just built here.

EXPOSE 80

# enable SSH service
RUN rm -f /etc/service/sshd/down
COPY ssh_keys.pub /tmp/ssh_keys.pub
RUN cat /tmp/ssh_keys.pub >> /root/.ssh/authorized_keys && rm -f /tmp/ssh_keys.pub

# we start local services (mysql, apache and exim) from rc.local
COPY config/rc.local /etc/

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
