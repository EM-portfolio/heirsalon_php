FROM php:7.2-apache

# mysqli 拡張をインストール
RUN docker-php-ext-install mysqli

# ServerName設定を追加
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
