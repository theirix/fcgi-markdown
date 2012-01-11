#!/usr/bin/ruby1.9.1

# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php 

require "cgi"
require "uri"
require "fcgi"
require 'bluecloth'

def document_wrapper(cssfile)
%{<html>
  <head>
		<title>%s</title>
		#{ cssfile ? '<link rel="stylesheet" type="text/css" href="' + cssfile + '"/>' : '' }
		<meta name="content-type" http-equiv="content-type" value="text/html; utf-8"/>
	</head>
  <body>
%s
  </body>
</html>
}
end

FCGI.each {|request|

	cssfile = nil
	begin
		uri = request.env['REDIRECT_URI']
		uri = request.env['REQUEST_URI'] if (uri or '') == '' 
		scriptfile = File.join(request.env['DOCUMENT_ROOT'], uri)
		raise 'No such file ' + scriptfile unless File.file?(scriptfile)

		title = URI(uri).path.split('/').reject(&:empty?).last
		title = title[0...title.rindex('.')] if title.rindex('.')

		local_dir = File.dirname(scriptfile)
		cssfile = %w{markdown style}.map { |s| s+'.css' }.find { |s| File.file?(File.join(local_dir,s)) }

		contents = File::read scriptfile
		bc = BlueCloth::new contents
		body = document_wrapper(cssfile) % [ title, bc.to_html ]
	rescue => e
		body = document_wrapper(cssfile) % [( title || 'Error'), "Error: " + CGI::escapeHTML(e.to_s) ]
	end

	out = request.out
	out.print "Content-Type: text/html\r\n" 
	out.print "\r\n" 
	out.print (body or "")
	request.finish
}
