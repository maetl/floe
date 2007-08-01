require 'rubygems'
require 'rake'
require 'commandline'

module Floe

  class Application < CommandLine::Application
    def initialize
      author    "Mark Rickerby"
      copyright "Coretxt, 2007"
      synopsis  "{options} <task> [context]"
      short_description "minimalist tool that flattens the effort of building web applications"
      long_description  "minimalist tool that flattens the effort of building web applications"
      options :help, :debug
      option  :names => "--in-file", :opt_found => get_args,
              :opt_description => "Input file for sample app.",
              :arg_description => "input_file"
      expected_args :file
    end
  
    def main
      puts "args:      #{args}"
      puts "--in-file: #{opt["--in-file"]}"
    end
    
    def abort_with_message(msg)
      puts "Floe aborted: " + msg
    end
    
    def run(command)
      begin
        Rake::Task[command].invoke
      rescue Exception => err
        abort_with_message(err)
       end
    end
  end

end