The application integrates with the SimBASE system. The application is used to register new documents in SimBASE and get information about a particular document by number.

## Built With
* [PHP 8.0](https://www.php.net/)
* [LibreOffice 7](https://www.libreoffice.org/)

## Requirements
* Server with minimum of 2 CPU cores, 4GB RAM and 64GB disk space
* OS [Oracle Linux 8](https://yum.oracle.com/oracle-linux-isos.html) with minimum amount of packages installed
* Domain name for web app, e.g. *app.example.com*
* SSL certificates for domain name (from commercial [CAs](https://en.wikipedia.org/wiki/Certificate_authority), e.g. [ssls.com](https://www.ssls.com/) or free from [Let’s Encrypt](https://letsencrypt.org/))
* Configure corporate firewall to allow inbound HTTP and HTTPS traffic from the Internet to the web server

## Installation
### Steps to create a new sudo user
Use the `adduser` command to add a new user to your system. Set and confirm the new user’s password at the prompt. A strong password is highly recommended. Follow the prompts to set the new user’s information. It is fine to accept the defaults to leave all of this information blank.
```shell
adduser app
```

### Installing packages
Execute following shell commands at your Oracle Linux system as root
```shell
dnf install -y nginx php-cli php-fpm php-soap php-xml php-opcache
```
```shell
wget https://downloadarchive.documentfoundation.org/libreoffice/old/7.4.7.2/rpm/x86_64/LibreOffice_7.4.7.2_Linux_x86-64_rpm.tar.gz
tar xzf LibreOffice_7.4.7.2_Linux_x86-64_rpm.tar.gz
dnf localinstall -y LibreOffice_7.4.7.2_Linux_x86-64_rpm/RPMS/*.rpm
ln -sf /opt/libreoffice7.4/program/soffice /usr/bin/soffice
```
### Uploading app files
Create site root directory and upload the site files there
```shell
mkdir -p /var/www/app
```
### Configuring the web app
Copy i_custom.php.example to i_custom.php and specify the following parameters: 
```php
$cfg['sbapi_iid'] = 'API_ID_IN_DEC_FORMAT';
$cfg['sbapi_url'] = 'API_URL';
$cfg['libreoffice_path'] = '/usr/bin/soffice';
```
### Setting up files and directories permissions
Execute following shell commands
```shell
chown -R app:app /var/www/app
chown -R apache:apache /var/www/app/src/public/upload
chown -R apache:apache /var/www/app/src/public/convertedFiles
chown -R apache:apache /var/www/app/tmp
chcon -R -t httpd_sys_content_rw_t /var/www/app/src/public/upload
chcon -R -t httpd_sys_content_rw_t /var/www/app/src/public/convertedFiles
chcon -R -t httpd_sys_content_rw_t /var/www/app/tmp
setsebool -P sudo setsebool -P httpd_execmem=1
```
### Setting up Nginx, replace app.example.com with your domain name
Sample Nginx configuration `/etc/nginx/conf.d/default.conf`
```nginx
# Redirect all HTTP traffic to HTTPS
server {
  listen 80 default_server;
  server_name _;

  location /.well-known/acme-challenge/ {
    root /var/www/letsencrypt/;
    default_type "text/plain";
    try_files $uri =404;
  }

  location / {
    return 301 https://$host$request_uri;
  }

}

server {
  listen 443;
  server_name app.example.com;

  ssl_certificate "/etc/letsencrypt/live/app.example.com/fullchain.pem";
  ssl_certificate_key "/etc/letsencrypt/live/app.example.com/privkey.pem";

  root /var/www/app/src/public;
  index index.php;

  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log;

  location / {
    try_files $uri $uri/ /index.php$is_args$args;
  }

  # Pass the PHP scripts to PHP-FPM
  location ~ \.php$ {
    try_files $fastcgi_script_name =404;
    fastcgi_pass unix:/run/php-fpm/www.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $request_filename;
  }

  # Disable php execute
  location /uploads/ {
    location ~ .*\.(php)?$ { deny all; }
  }

  location /convertedFiles/ {
    location ~ .*\.(php)?$ { deny all; }
  }

  error_page 404 /index.php;
  location ~ /\. {
    deny all;
  }

}
```
