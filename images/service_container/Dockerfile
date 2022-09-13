FROM ubuntu:20.04
MAINTAINER steven <steven.li@invengo.com>

ENV OS_LOCALE="en_US.UTF-8"
RUN ln -snf /usr/share/zoneinfo/$CONTAINER_TIMEZONE /etc/localtime && echo $CONTAINER_TIMEZONE > /etc/timezone
RUN apt update && apt install -y unzip openssh-server systemd nano iputils-ping net-tools sudo python3 python3-pip
RUN apt install -y apache2 php libapache2-mod-php php-mysql 
RUN apt-get clean
RUN pip3 install websocket-client
RUN pip3 install rel
RUN pip3 install paho-mqtt

RUN groupadd rfid
RUN useradd -m -s /bin/bash user && usermod -aG rfid user && echo "user:xcrf868" | chpasswd
RUN adduser user sudo

COPY entrypoint.sh /sbin/entrypoint.sh
RUN chmod +x /sbin/entrypoint.sh

EXPOSE 21
EXPOSE 22
EXPOSE 25
EXPOSE 80
EXPOSE 443
EXPOSE 3306
EXPOSE 1883
EXPOSE 8883
EXPOSE 5000
EXPOSE 6000
EXPOSE 7000
EXPOSE 8000
EXPOSE 9000

CMD ["/sbin/entrypoint.sh"]