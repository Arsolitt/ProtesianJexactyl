#!/usr/bin/env bash

role=${CONTAINER_ROLE}

if [ "$role" = "queue" ]; then

    echo "Running the queue..."
    exec php /home/app/artisan queue:work --verbose --tries=3

elif [ "$role" = "scheduler" ]; then

    echo "Running the scheduler..."
#    while true
#    do
#        php /home/app/artisan schedule:run --verbose --no-interaction &
#        sleep 60
#    done
    exec php /home/app/artisan schedule:work --verbose --no-interaction

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
