# $Id$
#
require 'rubygems'
Gem::manage_gems
require 'rake/gempackagetask'

spec = Gem::Specification.new do |s|
    s.platform  =   Gem::Platform::RUBY
    s.name      =   "floe"
    s.version   =   "0.0.1"
    s.author    =   "Mark Rickerby"
    s.email     =   "coretxt @nospan@ gmail.com"
    s.summary   =   "minimalist tool that flattens the effort of building web applications"
    s.files     =   FileList['lib/floe/*.rb', 'test/*'].to_a
    s.require_path  =   "lib"
    s.autorequire   =   "base"
    s.test_files = Dir.glob('test/*.rb')
    s.has_rdoc  =   true
    s.bindir = 'bin'
    s.executables << 'floe'
    s.extra_rdoc_files  =   ["README"]
    s.add_dependency("rake", ">=0.7.3")
    s.requirements << "MySQL 5.0 or greater"
    s.requirements << "PHP 5.2 or greater"
end

Rake::GemPackageTask.new(spec) do |pkg|
    pkg.need_tar = true
end

task :default => "pkg/#{spec.name}-#{spec.version}.gem" do
    puts "generated latest version"
end