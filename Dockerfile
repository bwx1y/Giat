FROM dunglas/frankenphp:php8.3-alpine@sha256:89eee730d674f1396a57cee60028ae3666e5a8fad55441d0d4ef7a2ea532af48

# Install ekstensi PostgreSQL
RUN install-php-extensions pdo_pgsql pgsql

WORKDIR /app

# Salin Caddyfile konfigurasi
COPY Caddyfile /etc/caddy/Caddyfile

# Salin script entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Salin seluruh source code
COPY . /app

EXPOSE 80 443 443/udp

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
