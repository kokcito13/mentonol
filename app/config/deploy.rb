set :application, "tags.hippl.net"
set :domain,      "tags.hippl.net"
set :user,         "hipplnet"
set :deploy_to,   "/var/www/hipplnet/data/www/capy"
set :app_path,    "app"
set :shared_files,      ["app/config/parameters.yml"]
set :web_path, "web"
set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :dump_assetic_assets, true
set :deploy_via, :remote_cache
set :ssh_options, { :forward_agent => true, :port => 6277 }
set :branch, "master"
set :copy_exclude, [".git", "spec"]

set :repository,  "https://github.com/kokcito13/tags"
set :scm,         :git

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :keep_releases,  3
set  :use_sudo, false
# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

#before 'symfony:composer:install', 'symfony:copy_vendors'
#before 'symfony:composer:update', 'symfony:copy_vendors'

before "symfony:cache:warmup", "symfony:doctrine:schema:update", "deploy:cleanup"

set :copy_vendors, true

namespace :symfony do
  desc "Copy vendors from previous release"
  task :copy_vendors, :except => { :no_release => true } do
    if Capistrano::CLI.ui.agree("Do you want to copy last release vendor dir then do composer install ?: (y/N)")
      capifony_pretty_print "--> Copying vendors from previous release"

      run "cp -a #{previous_release}/vendor #{latest_release}/"
      capifony_puts_ok
    end
  end
end