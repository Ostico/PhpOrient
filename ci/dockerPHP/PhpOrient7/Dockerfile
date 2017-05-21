FROM ubuntu:17.04

RUN apt-get update

# RUN apt-get -y --fix-missing install vim locate iputils-ping git curl wget net-tools psmisc screen
RUN apt-get -y install php-xml php-dev php-cli php-xdebug php-mbstring php-curl php-pdo php7.0-xsl vim locate \
    iputils-ping curl wget net-tools psmisc dstat  traceroute whois

COPY run.sh /tmp/run.sh
RUN chmod +x /tmp/run.sh

WORKDIR "/home/PhpOrient"
CMD ["/tmp/run.sh"]
