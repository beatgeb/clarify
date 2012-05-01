# Clarify - A Toolkit for the Frontend Workflow

Please keep in mind, that this is an alpha version. This affects several things
like missing authentication, security checks or sometimes architectural stuff.

*This version is not intended to be used in production!*

Learn more about Clarify in the following blog post (Text: German)

http://blog.namics.com/2012/03/clarify-ein-toolkit-fur-den-frontend-workflow.html

# Requirements

* PHP 5.2+
* MySQL 5+
* Apache (.htaccess Support)
* Modern Browser (with HTML5 Canvas Support)

# Installation

1. Create a database (e.g. "clarify") and configure its name in ```/application/config/config-private.php``` (create the file initially or copy config.php)
2. Run ```/application/db/create-tables.sql``` against the newly created database.
3. Create the folders ```/application/cache``` and ```/public/upload``` and give ```chmod 777``` to them
4. Open up the application in your browser and have fun

# Copyright & License

Copyright (c) 2012 Roger Dudler <roger.dudler@gmail.com>

Licensed under the MIT license:
http://www.opensource.org/licenses/MIT