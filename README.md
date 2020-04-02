# EasyTranslate Demo project with wordpress plugin integration
The current demo version of the plugin is not using the features from WPML, but when post is created, there are X amount new posts also created per each target language chosen. 
This was done on this way, just to visualize and demonstrate the automation of the platform and what to expect. When the webhook is received, the newly created posts content will be replaced with the translated content from the webhook

## Starting WP container
```bash
docker-compose -f docker-compose.yml -f dev.docker-compose.yml up -d --build
```
The demo application is using the the official Wordpress image from Dockerhub, and has the plugin inside the `wp-content/plugins` mounted to allow and show the currently developed plugin on wordpress

## Receiving webhooks
There is `ngrok` instance running, and this allows to receive remote webhooks to your local. Basically this service is proxing requests to your local machine.
On the `localhost:4040` there should be url for both HTTP and HTTPS. With this URL set up as webhook URL on the wordpress plugin, when the API receives translated task, it is sent to the provided URL.

## Sandbox
Note that the EasyTranslate API has sandbox environment. This is mocking up the translation, and on each 5 minutes assigned tasks are completed. This is useful for development.

## Security Notes
The secret values like passwords, api secret and access token are not encrypted before saving to database. But they should be.

## Getting translated content from the webhook
The translated content when task is completed and translated is found in the `target_content` attribute, and requires additional request with the already stored access token to the value of the `target_content` attribute.
This will return the translated content of the previously uploaded file

## Wordpress plugin features
  # New settings page after the plugin is updated. In here login credentials should be provided, and access token should be stored in order to create new projects.
  # New menu on the side bar when posting/editing post screen. Initially how this works is when the status is set to `publish` from any other status, the translation is triggered.
  # All logic for the API communication should be found on the `ApiService` class
  # It only interacts and has fields for sandbox environment 
