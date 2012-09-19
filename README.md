drupal-href-src-viewer
======================

A script that looks through drupal 6's node revisions table and gets all of the src and href attributes to help you find unwanted absolute links, hot-linked images, and images embedded in page nodes.

To use this script you must be at least partially familiar with php because configuration is handled baked into the source code.

To Use
======

1.) Edit line 37 to match your database parameters. I would recommend creating a select only database user so you know for sure that this script will not write to your database.
2.) You will probably want to change the function printMatches() on line 81 to fit your site. This function does the heavy lifting of adding classes based on criteria, so you will need to edit this function (and the css) to make it look the way you want. $classes[] is an array of class names to add to the list items. Since we wanted to see images that were being served from an old webserver 'www', we check to see if the src or href contains www.saintmarys.edu on line 84 and if it does, we add a class of www.
3.) Alter the key on line 140 to fit your needs.
4.) Lines 172-178 make calls to printFilesAndImagesFromDB(). The function takes a one element array where the key is the database name and the value is the path to where that install is located. So if you have a database called foo where the sites is located at foo.example.tld you would call printFilesAndmImagesFromDB(array('foo'=>'foo.example.tld'))
5.) Alter the css as you see fit to help you visualize the output.

Gotchas
=======

If your nodes have multiple revisions, every revision will be shown in this script.