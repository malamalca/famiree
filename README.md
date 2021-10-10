# Installation instructions


## Initial
```
sudo apt update
sudo apt full-upgrade

sudo timedatectl set-timezone Europe/Ljubljana
timedatectl status

sudo nano /boot/config.txt
   dtoverlay=disable-bt

sudo systemctl disable hciuart.service
sudo systemctl disable bluealsa.service
sudo systemctl disable bluetooth.service

sudo systemctl stop bluetooth
sudo systemctl disable avahi-daemon
sudo systemctl stop avahi-daemon
sudo systemctl disable triggerhappy
sudo systemctl stop triggerhappy

#enable www-data to reboot device
sudo visudo
   www-data ALL = NOPASSWD: /sbin/reboot, /sbin/halt

```

## Change default username PI to THORBELL

```
// set password for root
sudo passwd

sudo nano /etc/ssh/sshd_config
   PermitRootLogin yes
sudo service ssh restart


// login as root

usermod -l thorbell pi
usermod -m -d /home/thorbell thorbell
sudo passwd -l root
sudo nano /etc/ssh/sshd_config
  #PermitRootLogin yes
```

## Lighttpd

```
sudo apt-get -y install lighttpd

sudo apt install php7.3 php7.3-fpm php7.3-cgi
sudo lighttpd-enable-mod fastcgi-php
sudo service lighttpd force-reload

sudo apt-get install php7.3-curl php7.3-mbstring php7.3-pdo php7.3-sqlite3 php7.3-openssl php7.3-xml php7.3-intl php7.3-bcmath

sudo ln -s /home/pi/camera_wwwroot/ ./cam
```

### PAM for auth 
```
sudo apt-get install php-pear
sudo apt-get install php7.3-dev

sudo nano /etc/apt/sources.list
# uncomment sources
sudo apt-get build-dep pam

sudo apt-get install libpam0g-dev
sudo pecl install pam

# add extension "pam" to php.ini!!!
sudo service lighttpd restart

sudo cp /etc/pam.d/login /etc/pam.d/php
sudo nano /etc/pam.d/php
   auth       sufficient /lib/arm-linux-gnueabihf/security/pam_unix.so shadow nodelay
   account    sufficient /lib/arm-linux-gnueabihf/security/pam_unix.so
   
sudo chgrp www-data /etc/shadow
# for debug: cat /var/log/auth.log

```

## Samba (development)
```
sudo apt-get install samba samba-common-bin
sudo nano /etc/samba/smb.conf 
  [www]
  Comment = WWW
  Path = /home/thorbell/camera_wwwroot
  Browseable = yes
  Writeable = Yes
  only guest = no
  create mask = 0777
  directory mask = 0777
  Public = yes

sudo smbpasswd -a thorbell

sudo service smbd restart
sudo service nmbd restart
```

## UV4L
```
curl http://www.linux-projects.org/listing/uv4l_repo/lpkey.asc | sudo apt-key add -
deb http://www.linux-projects.org/listing/uv4l_repo/raspbian/stretch stretch main
echo 'deb http://www.linux-projects.org/listing/uv4l_repo/raspbian/stretch stretch main' | sudo tee -a /etc/apt/sources.list
sudo apt update
sudo apt install uv4l uv4l-raspicam uv4l-raspicam-extras
sudo apt install uv4l-webrtc
sudo service uv4l_raspicam restart
sudo service uv4l_raspicam status
sudo mv openssl.cnf /etc/uv4l/openssl.cnf
sudo nano /etc/systemd/system/uv4l_raspicam.service 
sudo systemctl daemon-reload && sudo service uv4l_raspicam start
openssl genrsa -out selfsign.key 2048 && openssl req -new -x509 -key selfsign.key -out selfsign.crt -sha256
mv selfsign.* /etc/uv4l/
sudo mv selfsign.* /etc/uv4l/

sudo service uv4l_raspicam start
sudo service uv4l_raspicam restart
```

## System Service
```
sudo nano thorbell.service /etc/systemd/system/thorbell.service

[Unit]
Description=Thorbell

[Service]
ExecStart=/usr/bin/php   /home/thorbell/camera_wwwroot/src/Console/thorbell.php
WorkingDirectory=/home/thorbell/camera_wwwroot
User=thorbell
Restart=always

[Install]
WantedBy=default.target  

systemctl daemon-reload
sudo systemctl enable thorbell.service
sudo systemctl start thorbell.service
```
