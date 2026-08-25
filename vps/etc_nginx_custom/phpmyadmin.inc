location /phpmyadmin {
	alias /usr/share/phpmyadmin/;

	location ~ /(libraries|setup|templates|locale) {
		deny   all;
		return 404;
	}

	location ~ /(.+\.(json|lock|md)) {
		deny   all;
		return 404;
	}

	location ~ ^/phpmyadmin/(.*\.php)$ {
		include       /etc/nginx/fastcgi_params;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME /usr/share/phpmyadmin/$1;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_param HTTPS on;
		fastcgi_param HTTP_X_FORWARDED_PROTO https;
		fastcgi_pass  unix:/run/php/www.sock;
	}

	location /phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|webp|js|ico|html|xml|txt))$ {
		root /usr/share/phpmyadmin/;
	}
}
