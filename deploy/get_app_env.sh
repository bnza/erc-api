#!/bin/sh

# Function to read a value from a .env file
read_env_value() {
  local key="$1"
  # POSIX-compliant way to read APP_ENV, handling quotes/whitespace
  local value=$(grep -E '^APP_ENV=' .env | cut -d= -f2- | sed "s/^['\"]//;s/['\"]$//" | tr -d '[:space:]')
  echo "$value"
}

# Read APP_ENV from .env
APP_ENV=$(read_env_value APP_ENV)

# Check if APP_ENV is set
if [ -z "$APP_ENV" ]; then
  echo "APP_ENV not set in .env. Defaulting to 'dev'."
  APP_ENV="dev"
fi

echo "$APP_ENV"
