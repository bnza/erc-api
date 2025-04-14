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

compose_file="${docker_compose_directory}/docker-compose.yml"
override_dev="${docker_compose_directory}/docker-compose.override.yml"
override_prod="${docker_compose_directory}/docker-compose.prod.yml"

# Validate consistency
if [ "$DOCKER_APP_ENV" != "$SYMFONY_APP_ENV" ]; then
  cat >&2 <<EOF
⚠️  Environment Mismatch Warning!
   Docker APP_ENV:    '$DOCKER_APP_ENV' (from .env)
   Symfony APP_ENV:   '$SYMFONY_APP_ENV' (from Symfony)

This may cause unexpected behavior. The environments must match.
EOF
  exit 1
fi

# Determine override file
case "$app_env" in
  "dev")
    override_file="$override_dev"
    ;;
  "prod")
    override_file="$override_prod"
    ;;
  *)
    echo "Invalid APP_ENV value: $app_env. Defaulting to development."
    override_file="$override_dev"
    ;;
esac

# Docker Compose up
echo "Starting Docker Compose (APP_ENV: $app_env)..."
echo_and_run /usr/bin/docker compose -f "${compose_file}" -f "${override_file}" up -d


# Wait for nginx
echo "Waiting for nginx to stop..."
/usr/bin/docker compose ps -q nginx | xargs /usr/bin/docker wait
