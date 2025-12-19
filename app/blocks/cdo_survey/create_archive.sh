#!/bin/bash

# Remove all node_modules directories within blocks/cdo_survey
find blocks/cdo_survey -type d -name "node_modules" -prune -exec rm -rf {} \;

# Create the zip archive, including the cdo_survey directory
zip -r blocks/cdo_survey.zip blocks/cdo_survey -x "blocks/cdo_survey/.git/*"

if [ $? -eq 0 ]; then
  echo "Created archive: blocks/cdo_survey.zip"
else
  echo "Error: Failed to create archive."
  exit 1
fi
