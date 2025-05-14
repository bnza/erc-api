#!/bin/bash
ln -sf "$(pwd)/deploy/git-hooks/pre-commit" "$(pwd)/.git/hooks/pre-commit"
chmod +x "$(pwd)/.git/hooks/pre-commit"
