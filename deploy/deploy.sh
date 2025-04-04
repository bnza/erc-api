#!/bin/sh

set -e

# Check if running with sudo
if [ -n "$SUDO_USER" ]; then
  # User who ran sudo
  docker_user="$SUDO_USER"
else
  # Running directly as the current user
  docker_user=$(whoami)
fi

# Check if current user is root
if [ ${docker_user} = "root" ]; then
  echo "Error: This script cannot be run as root."
  exit 1
fi

# Get script's parent directory
script_directory=$(dirname "$(readlink -f "$0")")

# Get script's parent directory
docker_compose_directory=$(dirname "${script_directory}")

services="docker-compose health-check messenger-consumer"
services_files=""

echo "Replacing template vars"
echo "DOCKER_USER: ${docker_user}"
echo "DOCKER_COMPOSE_DIRECTORY: ${docker_compose_directory}"

for service in $services; do
	# Generate absolute path to the template file, relative to the script's directory.
	service_file="/etc/systemd/system/medgreenrev.$service.service"
	template_path="$script_directory/systemd/medgreenrev.$service.service.template"
	echo "---------- $service_file ----------"
	# Generate service file using envsubst with absolute template path
	env DOCKER_USER="$docker_user" DOCKER_COMPOSE_DIRECTORY="$docker_compose_directory" envsubst < "$template_path" | sudo tee $service_file
	echo "-----------------------------------"
	services_files="$services_files\n$service_file"
done

# Reload systemd configuration
sudo systemctl daemon-reload

echo "Enable and start the services:"
# Enable and start the services
for service_file in $services_files; do
    echo "Processing: $service_services_files"
    sudo systemctl enable "$(basename "$service_file")"
    sudo systemctl start "$(basename "$service_file")"
done

echo "Systemctl services deployed."

