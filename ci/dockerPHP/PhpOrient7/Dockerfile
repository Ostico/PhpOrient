FROM ubuntu:18.04

RUN apt-get update

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get -y install php-xml php-dev php-cli php-xdebug php-mbstring php-curl php-pdo php-xsl vim locate \
    iputils-ping curl wget net-tools psmisc dstat  traceroute whois git unzip

RUN apt full-upgrade

COPY run.sh /tmp/run.sh
RUN chmod +x /tmp/run.sh

WORKDIR "/home/PhpOrient"
CMD ["/tmp/run.sh"]
