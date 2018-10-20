CXA Auth LW
======
CXA Auth LW is a lightweight, data-oriented full-stack web framework. It includes the necessary style and structure components to rapidly build and maintain small, good-looking, mobile-friendly forms and control panels.
It also provides a secure authentication system that's easy for users and administrators to use.

CXA Auth LW is not a complete drop in solution; it takes a bit of fiddling with. It just helps get you started with your webapp. Play around with the examples and templates to learn, as documentation is forthcoming.

CXA (CompetiX Architecture) is a data-oriented webapp framework derived from CompetixCMS, and is available in multiple versions depending on how much and what framework you want.

CXA Auth LW is full-stack, includes CXA UI, and is the second CXA component to be open-sourced.

CXA Auth LW is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 United States License, which is included in the file LICENSE.

CXA Auth LW is copyright 2016 James Rowley.

Installation
======
CXA Auth LW requires PHP 5.6 and a MySQL database.

At present, no documentation or setup script exists for CXA Auth LW; this will be fixed eventually. As such, you need to import the SQL file from the base directory. You must do this as a database admin.
This will create a database "cxa" with the proper format. Create a user with full permissions to this database and input the login information to php/config.php.

The default CXA Auth LW login is username: admin; password: default. **__Change the password after logging in__** (as admin) from the user management console (under the Administration menu, or users.php).