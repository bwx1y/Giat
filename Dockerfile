FROM dunglas/frankenphp:latest

# Install netcat untuk healthcheck database & ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y netcat-openbsd && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    intl \
    zip

# Set working directory
WORKDIR /app

# Copy seluruh file aplikasi
COPY . /app

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Copy dan beri izin eksekusi entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

# Gunakan entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Command default yang diteruskan ke ENTRYPOINT ($@)
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]