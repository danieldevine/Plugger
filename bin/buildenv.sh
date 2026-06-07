#!/bin/bash

cd wp || exit

wp config create --dbname='blart' --dbuser='root' --dbpass='' --dbhost='localhost'
wp config set WP_DEBUG_LOG true --raw --anchor='"stop editing" line. */' --placement=after
wp config set WP_ENVIRONMENT_TYPE development --anchor='"stop editing" line. */' --placement=after

wp db drop
wp db create

wp core install --url="http://blart.test" --title="Paul Blart Mall Cop 3 When" --admin_user="paul_blart" --admin_password="segway" --admin_email="paul.blart@example.com"

cp -r ../tests/blart wp-content/themes/
wp theme activate 'blart'

# set the site to noindex while in dev
wp option set blog_public 0

# remove the default WP tagline
wp option update blogdescription ''

# disable comments on new posts
wp option update default_comment_status closed

# disable pingbacks
wp option update default_pingback_flag 0
wp option update default_ping_status closed

wp post create --post_type=page --post_title='Home' --post_status=publish

wp config set WP_DEBUG true --raw --anchor='"stop editing" line. */' --placement=after

valet link blart
open 'http://blart.test'
