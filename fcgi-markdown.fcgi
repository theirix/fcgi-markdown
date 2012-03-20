#!/usr/bin/ruby1.9.1
# encoding: utf-8
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php 

require 'rubygems'
require 'rack'
require "cgi"
require "uri"
require 'bluecloth'

class App

	def document_wrapper(cssfile)
	%{<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
			<title>%s</title>
			#{ cssfile ? '<link rel="stylesheet" type="text/css" href="' + cssfile + '"/>' : '' }
		</head>
		<body>
	%s
		</body>
	</html>
	}
	end

	def make_response code, html
		body = document_wrapper(@cssfile) % [@title, html]
		[code, {"Content-Type" => "text/html"}, [body] ]
	end
	
	def make_response_error code, error
		@title = 'Error' unless @title
		make_response code, CGI.escapeHTML(error.to_s)
	end

	def call env
		@cssfile = nil
		@title = nil
		begin
			req = Rack::Request.new(env)
			STDERR.puts env.inspect

			uri = env['REDIRECT_URI']
			uri = env['REQUEST_URI'] if (uri or '') == '' 
			scriptfile = File.join(env['DOCUMENT_ROOT'], uri)
			return make_response_error(404, "No such file " + scriptfile) unless File.file?(scriptfile)

			@title = URI(uri).path.split('/').reject(&:empty?).last
			@title = @title[0...@title.rindex('.')] if @title.rindex('.')

			local_dir = File.dirname(scriptfile)
			@cssfile = %w{markdown style}.map { |s| s+'.css' }.find { |s| File.file?(File.join(local_dir,s)) }

			f = File.open(scriptfile, 'r:utf-8')
			begin
				contents = f.read
			ensure
				f.close
			end
			bc = BlueCloth::new contents
			return make_response(200, bc.to_html)
		rescue => e
			STDERR.puts "Exception: " + e.to_s
			raise e
			return make_response_error(500, e.to_s)
		end
	end
end

builder = Rack::Builder.new {
	use Rack::ShowExceptions
	use Rack::Lint
	run App.new
}

Rack::Handler::FastCGI.run builder
