# Excalibur CRUD Demo
This is a example project using the Excalibur framework, using this project and the Excalibur Docs(Coming soon) this project you should have a good idea of how to build a website with the framework.

This project is realeased before the framework for feedback form other developers, the template of the framework itself will be released once the docs are compelted to a useful state.

There is no live version of this project as hosting a live, public facing website seems like a bad idea.

#Techstack of this project
- Excalibur
- Tailwind

# Before Using
This framework is currently a heavy work in progress and is at the stage of technical demo, there are still planned security features missing from the current framework so please do not use on a public facing server without assessing the risks yourself.

# Currently Missing
- Finishing the update function for a guest book entry
- Responseive desing in the UI, this is a project focused on the functionality for developers most commonly using desktop / laptops
- .ENV file for easy config on diffrent servers

# Usage
This project has been tested with the XAMPP [https://www.apachefriends.org/download.html] webserver on ArcoLinux and the projec is setup to be accessed with the url ```http://localhost/ExcaliburCRUD/```. Please use `DansFramework.sql` to import the database tables, this isnt for the framework but it is required for this demo(As any projects built on the framework would have thier own DB tables)

If there are an issues please check the following
- Check to see if the base model is using the correct DB credentials `Framework/Model
/Model.php`
- Check to see if the URLs are corerct in `Framework
/index.php`, `Framework.js` and `index.php`. These should be the URL a browser to use not the filepaths
- Check to see if there is anything interfeering with the `Framework/.htaccess` this is needed to direct all routes through the `Framework/index.php` file which faciliates the PHP side of the framework
