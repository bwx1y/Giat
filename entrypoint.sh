#!/bin/sh
set -e

if [ -n "$DB_HOST" ]; then
  echo "Waiting for PostgreSQL database to be ready..."
  until nc -z -w5 "$DB_HOST" "$DB_PORT"; do
    echo "PostgreSQL is not ready yet, retrying in 2 seconds..."
    sleep 2
  done

  echo "PostgreSQL is ready! Running migrations..."
  php migration up || echo "Migration failed or no pending migrations to apply."
fi

exec "$@"
