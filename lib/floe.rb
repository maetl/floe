module Floe

  class Application < CommandLine::Application
    def initialize
      use_replay
      author    "Mark Rickerby"
      copyright "Coretxt, 2007"
      synopsis  "{options} <task> [context]"
      short_description ""
      long_description  ""
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
  end

end