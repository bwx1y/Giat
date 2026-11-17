FROM dunglas/frankenphp:php8.3-bookworm

RUN install-php-extensions pdo_pgsql pgsql

WORKDIR /app
COPY . /app
COPY deploy/Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p /app/log /var/lib/php/sessions \
    && printf 'session.save_path=/var/lib/php/sessions\nsession.cookie_httponly=1\nsession.cookie_secure=1\nsession.cookie_samesite=Lax\ndisplay_errors=Off\nlog_errors=On\n' \
       > /usr/local/etc/php/conf.d/production.ini

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
