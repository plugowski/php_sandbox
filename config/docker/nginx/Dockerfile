FROM nginx:1.11

MAINTAINER plugowski <pawelugowski@gmail.com>

RUN rm /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/nginx.conf
COPY conf.d/sandbox.conf /etc/nginx/conf.d/sandbox.conf

RUN usermod -u 1000 www-data
