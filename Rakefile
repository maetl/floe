# ...........................................................................................
# .. Floe ...................................................................................
# ...........................................................................................
# $Id$
#
require 'rubygems'
Gem::manage_gems
require 'rake/gempackagetask'
require 'rake/testtask'

# ...........................................................................................
# build gem package
spec = Gem::Specification.new do |s|
    s.platform  =   Gem::Platform::RUBY
    s.name      =   "floe"
    s.version   =   "0.0.1"
    s.author    =   "Mark Rickerby"
    s.email     =   "maetl[at]coretxt.net.nz"
    s.summary   =   "minimalist tool that flattens the effort of building web applications"
    s.files     =   FileList['lib/*.rb','ext/**/**','test/**/**'].to_a
    s.require_path  =   "lib"
    s.autorequire   =   "project"
    s.test_files = Dir.glob('test/lib/*.rb')
    s.has_rdoc  =   true
    s.bindir = 'bin'
    s.executables << 'floe'
    s.extra_rdoc_files = ["README"]
    s.add_dependency("rake", ">=0.7.3")
    s.add_dependency("commandline",">=0.7.10")
    s.add_dependency("activerecord",">=1.15.3")
    s.requirements << "MySQL 5.0 or greater"
    s.requirements << "PHP 5.2 or greater"
    s.requirements << "simpletest installed on PHP include_path"
end

Rake::GemPackageTask.new(spec) do |pkg|
    pkg.need_tar_gz = true
    pkg.need_zip = true
end

desc "build the gem package to latest specification"
task :build => "pkg/#{spec.name}-#{spec.version}.gem" do
    puts "generated latest version"
end

# ...........................................................................................
# ruby tests
desc "run all ruby level tests"
Rake::TestTask.new do |t|
   t.libs << 'test'
   t.test_files = FileList['test/lib/*_test.rb']
   t.verbose = true
   t.warning = true
end

# ...........................................................................................
# php tests
desc "run all php level tests"
task :phptest do |t|
  puts `php -f test/ext/test_suite.php`
end

# ...........................................................................................
# defaults
task :alltests => [:test, :phptest]
task :default => :alltests