require 'test/unit'
require 'floe'

class ApplicationCommandTest < Test::Unit::TestCase
  
  def test_stub_commands_must_exist
    assert Floe::Application.run('status')
    assert Floe::Application.run('todo')
    assert Floe::Application.run('configure')
    assert Floe::Application.run('install')
  end
  
end