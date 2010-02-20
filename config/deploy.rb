set :application, "foodcoop"
set :repository,  "git@github.com:traceyambrose/foodcoop.git"
set :deploy_to, "/home/ttnz/sites/#{application}"

set :scm, :git

set :single_server, "foodcoop.tt.craigambrose.com"

role :web, single_server                          # Your HTTP server, Apache/etc
role :app, single_server                          # This may be the same as your `Web` server
role :db,  single_server

set :username, 'ttnz'

# If you are using Passenger mod_rails uncomment this:
# if you're still using the script/reapear helper you will need
# these http://github.com/rails/irs_process_scripts

# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end