#unless defined? $lang
#  require 'floe/en'
#end
require 'yaml'

class Hash #:nodoc:
  def method_missing(meth,*args)
    if /=$/=~(meth=meth.id2name) then
      self[meth[0...-1]] = (args.length<2 ? args[0] : args)
    else
      self[meth]
    end
  end
end

class String

    def red; colorize(self, "\e[1m\e[31m"); end
    def green; colorize(self, "\e[1m\e[32m"); end
    def dark_green; colorize(self, "\e[32m"); end
    def yellow; colorize(self, "\e[1m\e[33m"); end
    def blue; colorize(self, "\e[1m\e[34m"); end
    def dark_blue; colorize(self, "\e[34m"); end
    def pur; colorize(self, "\e[1m\e[35m"); end
    def colorize(text, color_code)  "#{color_code}#{text}\e[0m" end

end

def colorize(text, color_code)
  "#{color_code}#{text}\e[0m"
end

def red(text); colorize(text, "\e[31m"); end
def green(text); colorize(text, "\e[32m"); end
def white(text); colorize(text, "\e[37m"); end
def yellow(text); colorize(text, "\e[33m"); end
def cyan(text); colorize(text, "\e[36m"); end

# This package is a command line tool and component library
# for rapid prototyping web applications. For more information,
# see the Introduction[http://code.google.com/p/floe/]
# on Google Code.
module Floe

  # Runs the command invocation
  class Application
    def self.run(cmd, args='')
      unless cmd then cmd = :help; end
      begin
        CommandIndex.send(cmd, args)
        true
      rescue Exception => e
        puts e.to_s
        false
      end
    end
  end
  
  # Map of registered commands
  class CommandIndex
    
    # check the status of current project
    def self.status(event)
      event
    end
    
    # configure the main floe tools
    def self.configure(event)
      event
    end

    # install something (what?)
    def self.install(event)
      event
    end
    
    def self.help(event)
      Raise::message(File.read('HELP'))
    end
    
    # the command doesn't exist... we could delegate to something smarter here perhaps
    def self.method_missing(method, args)
      Raise::no_command(method)
    end
    
  end
  
  # Raise a console event
  module Raise
    
    def self.message(msg)
      puts green(msg)
    end
    
    # Raised when the user specific configuration file is not used 
    def self.not_installed
      puts cyan("Floe") + white(" is not installed! You're running hot on ") + red("Rake") + white("!")
    end
    
    # Info message for generated file assets
    def self.building(path)
      puts white("Building: ") + yellow(path)
    end
  
    @@prefix = "Floe aborted with "
    
    # Raised when the required master configuration is missing
    def self.default_missing(path)
      puts white(@@prefix) + white("no default configuration provided.")
      exit    
    end

    # Raised when a non-existent command is given
    def self.no_command(cmd)
      puts white(@@prefix) + white("no command target for: ") + red(cmd)
      exit
    end
    
    # Raised when a required resource or asset file is missing
    def self.missing_resource(path)
      puts white(@@prefix) + white("missing build resource: ") + red(path)
      exit
    end
    
    # Raised when a file can't be parsed, for whatever reason
    def self.parse_error(path)
      puts white(@@prefix) + white("parse error in: ") + red(path)
      exit
    end
    
  end
  
  # Read-only map of properties from a build configuration file
  class BuildEnvironment < Hash
    attr_reader :name, :version
    # name of the project
    def name
      self['project']['name']
    end
    # current version
    def version
      self['project']['version']
    end
    # package name for current version
    def pkg_name
      self.name + "-" + self.version
    end
    # path to location of current exported build files
    def build_dir
      self.release.archive_dir + "/builds/" + self.pkg_name
    end
    # path to location of build versioning metadata
    def version_dir
      self.release.archive_dir + "/builds/.versions/" + self.pkg_name
    end
  end
  
  # Data format wrappers
  module Format
    # YAML format
    class YML
      def self.read(fname)
        begin
          out = YAML::load_file(fname)
        rescue ArgumentError => e
          Floe::Raise.parse_error(fname)
        end
        out
      end
      def self.write(fname, contents)
        File.open(fname, "w") { |f| f.puts contents.to_yaml }
      end
    end
    
  end
end
