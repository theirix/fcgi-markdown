# fcgi-markdown - ruby Rack/FastCGI handler for Markdown

__fcgi-markdown__ is a simple ruby Rack/FastCGI handler for Markdown.

Provided script works is a Rack application that is configured as a FastCGI server.
Script produces XHTML output by Markdown templates.

## Styles

Markdown do not specify styles and meta information.

Script allows to pick a CSS file from the directory with a markdown template.
CSS name must be as `markdown.css` or `style.css`. fcgi-markdown do not include any
css if CSS was not found.

Script evaluates title of a document as a last component of an URI. Unfortunalety
it's impossible to store meta-information in xattrs in a common portable way.

## Dependencies

 * `bluecloth` gem
   
   It's bluecloth 2. Can be found at [homepage](http://deveiate.org/projects/BlueCloth)
   or [github mirror](https://github.com/ged/bluecloth)

 * `rack`

	You may need a fcgi gem. `ruby-fcgi` is a fork of `fcgi` for ruby 1.9. Check it at the [github](https://github.com/saks/ruby-fcgi)


## Usage with Apache2

  Example for a debian system.

  * Install dependencies

        gem install rack bluecloth ruby-fcgi

  * Install `mod_fastcgi` from the package `libapache2-mod-fastcgi`

        apt-get install libapache2-mod-fastcgi

  * Copy or symlink a `fcgi-markdown.fcgi` to FastCGI script directory

        cp fcgi-markdown.fcgi /usr/lib/cgi-bin/

  * Make sure `cgi-bin` is enabled for execution (check *ExecCGI* option)

  * Add following lines to the site config

        AddHandler fcgi-markdown .md 
        Action fcgi-markdown /cgi-bin/fcgi-markdown.fcgi
        
  * Restart apache


## Usage with lighttpd

  * Install dependencies

        gem install bluecloth ruby-fcgi

  * Place `fcgi-markdown.fcgi` anywhere you like

  * Enable `mod_fastcgi`

    Usually it is in `/etc/lighttpd/conf-available/10-fastcgi.conf`

  * Add following lines to the config

        fastcgi.server = (".md" => ((
            "bin-path" => "/var/www/fcgi-markdown.fcgi",
            "kill-signal" => 10,
            "port" => 1027))
        )
        
  * Restart lighttpd

## License

*Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php*


Copyright (c) 2011 Eugene Seliverstov 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Author

Eugene Seliverstov ([http://omniverse.ru](http://omniverse.ru))

<!--vim: ft=markdown, expandtab -->
