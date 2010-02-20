set :application, "foodcoop"
set :repository,  "git@github.com:traceyambrose/foodcoop.git"
set :deploy_to, "/home/ttnz/sites/#{application}"

set :scm, :git
set :use_sudo, false

set :single_server, "foodcoop.tt.craigambrose.com"

role :web, single_server                          # Your HTTP server, Apache/etc
role :app, single_server                          # This may be the same as your `Web` server
role :db,  single_server

set :user, 'ttnz'

# If you are using Passenger mod_rails uncomment this:
# if you're still using the script/reapear helper you will need
# these http://github.com/rails/irs_process_scripts

namespace :deploy do
  task :start do ; end
  task :stop do ; end
  task :restart do ; end
  task :finalize_update do ; end
  
  task :write_htaccess do
    dirs = %w(admin ajax members producers)
    source_file = "#{current_path}/config/production.htaccess"
    shop_path = "#{current_path}/public_html/shop/"
    paths = [shop_path]
    paths += dirs.map {|dir| shop_path + dir}
    paths.each do |path|
      run "cp #{source_file} #{path}"
    end
  end
end

after :deploy, "deploy:cleanup"