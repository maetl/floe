require 'rubygems'
require 'rake'

#unless defined? $lang
#  require 'floe/en'
#end

module Floe

  class Application
    def self.run(cmd, args='')
      begin
        CommandIndex.send(cmd, args)
        true
      rescue Exception => e
        puts e
        false
      end
    end
  end
  
  class CommandIndex
    
    def self.status(event)
      event
    end
    
    def self.todo(event)
      event
    end
    
    def self.configure(event)
      event
    end

    def self.install(event)
      event
    end    
    
    def self.method_missing(method, args)
      puts UNKNOWN_COMMAND
    end
  end

end