This is the Casimir framework developed by Daniel Wolff 
and Guillaume Bellec at City University London and KTH Stockholm.

% for a description of the framework please see
Wolff, D., Bellec, G., Friberg, A., MacFarlane, A., and Weyde, T. (2014). Creating audio based experiments as social web games with the CASimIR framework, In: AES 53rd Int. Conf. on Semantic Audio, 10 pages, Jan. 2014.

In order to run the framework the following server setup is recommended:
*Apache 5.17 or greater with PHP, and SOAP, CURL extensions enabled.
*MYSQL database with user for writing into tables.

To install, please use the following steps:
*Checkout this working copy into a directory in your server.
*Unzip / decompress the Archives "LimeJS" and "Zend" into the toolboxes folder
*Import the database in install/database_dist.zip into your mysql database

*Insert the path and MYSQL user information into the following three files:
->camir_gameClient/configClient.php
->camir_gameServer/config.php
->camir_gameServer/config.php
->server/ooo/configServer.php