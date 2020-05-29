#!/bin/sh
# Run this first time using this repo

GIT_DIR=`git rev-parse --git-common-dir 2> /dev/null`

echo
echo

# Check if we are in a git repo
if [[ "$GIT_DIR" == "" ]]; then
  echo "This does not appear to be a git repo."
  exit 1
fi

# Check if there is an existing pre commit hook
if [[ -f "$GIT_DIR/hooks/pre-commit" ]]; then
  echo "There is already a pre-commit hook installed. Delete it first."
  echo
  echo "    rm '$GIT_DIR/hooks/pre-commit'"
  echo
  exit 2
fi

# Download pre commit hook
FILE=${1:-pre-commit}

echo "Downloading $FILE hook from https://raw.githubusercontent.com/Jari-B/githooks/master/pre-commit"
echo

curl -fL -o "$GIT_DIR/hooks/pre-commit" "https://raw.githubusercontent.com/Jari-B/githooks/master/$FILE"
if [[ ! -f "$GIT_DIR/hooks/pre-commit" ]]; then
echo "Error downloading pre-commit script!"
exit 3
fi

# Make script executable
chmod +x "$GIT_DIR/hooks/pre-commit"

echo
echo "You're all set! Please check .git/hooks/pre-commit."
echo
exit 0