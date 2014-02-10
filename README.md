Ospari
======
**Ospari is a free and open source PHP based blogging software that can use Ghost themes.**  Ospari is developed and maintained by @wrahim.
This is the first release with very basic, but solid features to show, that we can parse themes, which are created with Handlebars.js. <a href="http://blog.ospari.org">Demo</a> 
Visit <a href="http://www.ospari.org">Ospari.org</a> for details and follow <a href="https://twitter.com/Ospari">@Ospari</a> on Twitter for updates.

File upload and tagging are currently not supported. We would enable tagging and file upload in next version very soon. 

##Getting started

0. Copy Ospari to a directory of your choice
1. Create a MySQL database
2. Rename core/application.config.php-dist to application.config.php
3. Enter database name, password and username
4. Open your browser, enter your blog URL and press enter.   

##Directory Structure
We have chosen to use an unusual directory structure for Ospari. The core application actually doesn’t belong to the public folder. We have put everything into the public folder to make the installation for the “normal users” very easy. With this directory structure you can also install Ospari in a subdirectory. 

We would remove vender packages in the next releases, but we would still provide a single package for the end users.  


##License
Copyright (C) 2014 28h Lab - Released under the MIT License.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 
