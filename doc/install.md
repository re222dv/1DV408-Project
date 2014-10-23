# Install Instructions

## Requirements
1. PHP 5.5
1. MySQL
1. A top level path (the application does not support being served from a subdirectory)

## Setup the web server
All requests except for those to /static should be handled by index.php with the path
set to the GET parameter path.

Configuration example for Nginx:
```
server {
    server_name _;
    listen 80 default;
    root "/var/www/umls/";
    index index.php;

    location /static {
        try_files $uri 404;
    }

    location ~* \.php$ {
        expires -1;
        include fastcgi_params;
        fastcgi_index index.php;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
    }

    ## Default location
    location / {
        rewrite ^ /index.php?path=$uri&$args last;
    }
}
```

## Set up the database
1. Create a database user, a database and give the database user `CREATE`, `DELETE`, `INSERT`,
`SELECT` and `UPDATE` privileges.
1. Copy `config.php.example` to `config.php` and enter your database settings.
