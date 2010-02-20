desc "Copy the production database onto this machine"
task :sync do
  host = 'ttnz@tt.craigambrose.com'
  db_config = {:username => 'foodcoop', :database => 'foodcoop', :password => 'bootlace'}
  local_file = "./config/production_data.sql"
  
  system "ssh #{host} \"mysqldump -u #{db_config[:username]} -p#{db_config[:password] } --add-drop-table #{db_config[:database]} > ~/dump.sql\""
  system "rsync -az --progress #{host}:~/dump.sql #{local_file}"
  system "mysql -u #{db_config[:username]} -p#{db_config[:password]} #{db_config[:database]} < #{local_file}"
  rm_rf local_file
end