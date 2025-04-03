#!/bin/sh

set -e

# Check if running with sudo
if [ -n "$SUDO_USER" ]; then
  # User who ran sudo
  ACTUAL_USER="$SUDO_USER"
else
  # Running directly as the current user
  ACTUAL_USER=$(whoami)
fi

# Check if current user is root
if [ ${ACTUAL_USER} = "root" ]; then
  echo "Error: This script cannot be run as root."
  exit 1
fi

# Get script's parent directory
SCRIPT_DIR=$(dirname "$(readlink -f "$0")")

# Get current user
DOCKER_USER=${ACTUAL_USER}

# Get script's parent directory
DOCKER_COMPOSE_DIRECTORY=$(dirname "${SCRIPT_DIR}")

# Generate absolute path to the template file, relative to the script's directory.
TEMPLATE_PATH="$SCRIPT_DIR/systemd/medgreenrev.messenger-consumer.service.template"

echo "Replacing template vars"
echo "DOCKER_USER: ${DOCKER_USER}"
echo "DOCKER_COMPOSE_DIRECTORY: ${DOCKER_COMPOSE_DIRECTORY}"
echo "##########"
# Generate service file using envsubst with absolute template path
env DOCKER_USER="$DOCKER_USER" DOCKER_COMPOSE_DIRECTORY="$DOCKER_COMPOSE_DIRECTORY" envsubst < "$TEMPLATE_PATH" | sudo tee /etc/systemd/system/medgreenrev.messenger-consumer.service
echo "##########"
## Re
#envsubst < "${TEMPLATE_PATH}" | sudo tee /etc/systemd/system/medgreenrev.messenger-consumer.service

# Reload systemd configuration
sudo systemctl daemon-reload

# Enable and start the service
sudo systemctl enable medgreenrev.messenger-consumer.service
sudo systemctl start medgreenrev.messenger-consumer.service

echo "Messenger consumer service deployed."

