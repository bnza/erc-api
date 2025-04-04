#!/bin/bash

# Get script's parent directory
script_directory=$(dirname "$(readlink -f "$0")")

# Get script's parent directory
docker_compose_directory=$(dirname "${script_directory}")

if [ $(pwd) != $docker_compose_directory ]; then
    echo "Current directory is different from $docker_compose_directory"
    exit 2
fi

container_id=$(/usr/bin/docker compose ps -q nginx)

if [ -z "$container_id" ]; then
  echo "Nginx container not found."
  exit 1
fi

health_status=$(/usr/bin/docker inspect --format='{{.State.Health.Status}}' "$container_id")
if [ -n "$health_status" ]; then
    if [ "$health_status" != "healthy" ]; then
        exit 1 # Container is unhealthy
    fi
fi

exit 0
