Simple PHP Class that allows you to call DigitalOcean V2 API
============================================================

With this simple PHP class you can call DigitalOcean's v2 API.

!!BE CAREFUL AND MAKE SURE TO SECURE THE CODE OR PLACE IT IN FOLDERS ONLY ACCESSIBLE BY ROOT. 
THE DIGITALOCEAN API TOKEN GIVES THE CODE ACCESS TO EVERYTHING WITHIN YOUR DIGITALOCEAN ACCOUNT!!!

AS OF THIS MOMENT YOU CAN'T RESTRICT THE API ACCESS TO LET'S SAY DOMAIN RECORDS.


Usage
------------

requirements: php v5.4+ with php_curl extension installed

The steps are:

1. Generate a token one from https://cloud.digitalocean.com/account/api/tokens
2. Load the orbisius-integration-digitalocean-api.class.php file.
3. Pass the API token & your email to the constructor.
4. Make the call to the API.
5. Are you feeling generous? [Buy me a beer or coffee then](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SS49ABSAHMMPG). This really keeps me going ;) 

Feel free to check the examples folder. You will need to update config.php with your API token.


Consultation & Customizations (Paid)
------------------------------------

If you like this software and/or want it adapted for your busines needs let us know.

[Request a free quote](https://orbisius.com/free-quote?utm_source=digitalocean-api)


License
-------

DigitalOceanV2 is released under the MIT License. See the bundled
[LICENSE](https://github.com/orbisius/orbisius-integration-digitalocean-api/blob/master/LICENSE) file for details.



Support
-------

[Please open an issue in github](https://github.com/orbisius/orbisius-integration-digitalocean-api/issues)

