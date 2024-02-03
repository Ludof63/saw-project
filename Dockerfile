FROM php:7.4.3-apache
# bz2 curl dom ffi ftp gd mbstring mysqlnd phar readline simplexml xml xmlreader xmlwriter xsl zip
ENV packages="calendar ctype exif fileinfo gettext iconv json mysqli opcache pdo pdo_mysql posix shmop sockets sysvmsg sysvsem tokenizer"
RUN docker-php-ext-install $packages && docker-php-ext-enable $packages && docker-php-source delete