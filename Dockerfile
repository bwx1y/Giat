FROM dunglas/frankenphp:latest-php8.3-alpine

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
