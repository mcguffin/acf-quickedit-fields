#!/bin/bash

# @see https://stackoverflow.com/questions/14702148/how-to-fire-a-command-when-a-shell-script-is-interrupted

exitfn () {
    trap SIGINT
    npm run test stop
    exit
}

trap "exitfn" INT            # Set up SIGINT trap to call function.

npm run test start
npm run dev

trap SIGINT                  # Restore signal handling to previous before exit.
