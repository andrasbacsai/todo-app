#!/bin/bash

# Default values
NO_CACHE=false
PORT=8888
CONTAINER_NAME="php"
IMAGE_NAME="php"
ENV_FILE=".env.production"

# Parse arguments
while [[ "$#" -gt 0 ]]; do
    case $1 in
        --no-cache) NO_CACHE=true ;;
        --port) PORT="$2"; shift ;;
        *) echo "Unknown parameter: $1"; exit 1 ;;
    esac
    shift
done

# Build the image
echo "Building Docker image..."
if [ "$NO_CACHE" = true ]; then
    docker build --no-cache -t $IMAGE_NAME -f docker/production/Dockerfile .
else
    docker build -t $IMAGE_NAME -f docker/production/Dockerfile .
fi

# Check if build was successful
if [ $? -eq 0 ]; then
    echo "Build successful. Starting container..."

    # Check if container already exists
    if docker ps -a | grep -q $CONTAINER_NAME; then
        echo "Removing existing container..."
        docker rm -f $CONTAINER_NAME
    fi

    # Run the container
    docker run --rm -ti \
        -p $PORT:8080 \
        --env-file $ENV_FILE \
        --name $CONTAINER_NAME \
        $IMAGE_NAME
else
    echo "Build failed!"
    exit 1
fi
