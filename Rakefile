# ...........................................................................................
# .. Floe ...................................................................................
# ...........................................................................................
# $Id$
#
require 'rubygems'
Gem::manage_gems
require 'rake/gempackagetask'
require 'rake/testtask'
require 'rss/2.0'
require 'rss/maker'
require 'net/ftp'
require 'lib/floe.rb'

$conf = Floe::Format::YML.read("buildfile")

# ...........................................................................................
# build ruby gem package
spec = Gem::Specification.new do |s|
    s.platform  =   Gem::Platform::RUBY
    s.name      =   $conf.name
    s.version   =   $conf.version
    s.author    =   $conf.project.author.name
    s.email     =   $conf.project.author.email
    s.summary   =   $conf.project.info
    s.files     =   FileList['lib/*.rb','ext/php/**','test/**/**'].to_a
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

desc "install the latest gem locally"
task :install do
  sh "sudo gem install pkg/floe-#{$conf.version}"
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
# build php package
desc "generate PHP documentation"
task :phpdoc do
  current_doc_dir = $conf.release.phpdoc_dir + "/" + $conf.version
  if File.exists?(current_doc_dir)
    rm_r current_doc_dir
  end
  mkdir current_doc_dir
  sh "phpdoc -d #{$conf.build_dir}/classes -t #{current_doc_dir} -dn floe -o HTML:Smarty:PHP -ti '#{$conf.project.title} API #{$conf.version}'"
end

# extract this helper to lib/floe
def phing(f, t='')
  sh "phing -f #{f} #{t.to_s}"
end

desc "export a build of the current revision"
task :phpbuild do
  rm_r $conf.build_dir
  phing "ext/php/build.xml", :current_revision
end

desc "export and upload a snapshot build of the current revision"
task :phpsnap => :phpbuild do
  phing "ext/php/build.xml", :snapshot_package
  
  snapshot_pkg = Dir["/Users/maetl/Projects/Floe/Code/packages/snapshots/*.zip"].max { |a,b| File.mtime(a) <=> File.mtime(b) }
  make_snapshot_feed(File.basename(snapshot_pkg))
  
  Net::FTP.open($conf.release.ftp.host) do |ftp|
    ftp.login($conf.release.ftp.user, $conf.release.ftp.pass)
    ftp.chdir($conf.release.ftp.dir)
    ftp.putbinaryfile(snapshot_pkg)
    ftp.puttextfile("/Users/maetl/Sites/coretxt-os/pkg/floe/builds.xml")
  end
end

task :testsnaps do
  puts revision_snapshot()
end

#
# get a snapshot log of the current revision
def revision_snapshot()
  start_rev = File.read($conf.version_dir + "/StartRev").strip
  end_rev = File.read($conf.version_dir + "/EndRev").strip
  log_rev = (start_rev == end_rev) ? 'HEAD' : end_rev + ":" + start_rev
  txt = `svn log -r #{log_rev} #{$conf.source.svn.host}`
  txt
end

#
# Adds a new snapshot build to the project RSS feed
def make_snapshot_feed(snapshot_file)
  feed_xml = "/Users/maetl/Sites/coretxt-os/pkg/floe/builds.xml"
  log_txt = revision_snapshot()
  
  feed = RSS::Parser.parse(File.read(feed_xml)) do |maker|
    maker.channel.about = "http://os.coretxt.net.nz/pkg/floe/builds.xml"
    maker.channel.title = "Floe::PHP"
    maker.channel.description = "latest snapshots from floe/trunk/ext/php"
    maker.channel.language = "en-nz"
    maker.channel.copyright = "Coretxt (Public Domain)."
    maker.channel.link = "http://os.coretxt.net.nz/code/floe"
    maker.channel.date = Time.now
    maker.channel.generator = "coretxt.os.labs"
    maker.items.do_sort = true
    maker.items.new_item do |item|
      item.link = "http://os.coretxt.net.nz/pkg/floe/#{snapshot_file}"
      item.title = "Snapshot: #{snapshot_file}"
      item.date = Time.now
      item.description = log_txt
      item.author = "os@coretxt.net.nz"
    end
  end
  File.open(feed_xml, "w") { |f| f.puts feed.to_s }
end

desc "run all php level tests"
task :phptest do |t|
  puts `php -f test/ext/test_suite.php`
end

# ...........................................................................................
# defaults
task :alltests => [:test, :phptest]
task :default => :alltests