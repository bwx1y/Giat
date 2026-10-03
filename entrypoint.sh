#!/bin/sh
set -e

echo "Waiting for PostgreSQL database to be ready..."

# Wait until the PostgreSQL port is reachable
until nc -z -v -w30 "$DB_HOST" "$DB_PORT"; do
  echo "PostgreSQL is not ready yet, retrying in 2 seconds..."
  sleep 2
done

echo "PostgreSQL is ready! Running migrations..."
php migrate up || echo "Migration failed or no pending migrations to apply."

echo "Starting FrankenPHP Server..."
exec "$@"