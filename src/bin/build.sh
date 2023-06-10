#!/bin/bash

ignore="vendor/*,node_modules/*,lib/wp-package-updater-lib/wp-package-updater,lib/wp-package-updater-lib/plugin-update-checker/,sources/,*.asset.php,*/*/*.asset.php,*.map,includes/fullcalendar/fullcalendar.*"

PGM=$(basename $0)
trap 'previous_command=$this_command; this_command=$BASH_COMMAND' DEBUG
trap 'ret=$?; [ $ret -ne 0 ] && echo "$PGM: $previous_command failed (error $ret)" && exit $ret || echo "$PGM: success"' EXIT

minphp=$(grep "Requires PHP:" readme.txt | sed "s/.*PHP: *//")
echo "# check compatibility with minimum PHP version required $minphp"
phpcs -p . --standard=PHPCompatibility --ignore=$ignore,*js,*css --runtime-set testVersion ${minphp}- \
&& echo "# normalize code" \
&& { phpcbf --standard=WordPress --ignore=$ignore ./ || phpcbf --standard=WordPress --ignore=$ignore ./ ; } \
&& echo "# $minphp composer update" \
&& php$minphp /usr/local/bin/composer update --no-dev \
&& echo "# npm build" \
&& npm run build
