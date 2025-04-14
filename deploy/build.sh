#!/bin/sh

echo_and_run() {
  printf "%s\n" "→ $*"
  if ! "$@"; then
    echo "Error: Command failed!" >&2
    exit 1
  fi
}

# Get script's  directory
script_directory=$(dirname "$(readlink -f "$0")")

# Get script's parent directory
docker_compose_directory=$(dirname "${script_directory}")

# Get APP_ENV
app_env=$(sh "${docker_compose_directory}/deploy/get_app_env.sh")

# Build the Docker image based on APP_ENV
case "$app_env" in
  "dev")
    echo "Building development image..."
    echo_and_run docker compose -f docker-compose.yml -f docker-compose.override.yml build
    ;;
  "prod")
    echo "Building production image..."
	echo_and_run docker compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache php
    ;;
  *)
    echo "Invalid APP_ENV value: $app_env. Building development image by default."
    echo_and_run docker compose  -f docker-compose.yml -f docker-compose.override.yml build
    ;;
esac


echo "Docker image build complete."
