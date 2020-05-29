<?php
namespace Deployer;

require 'recipe/common.php';
inventory('hosts.yml');

// Project name
set('project_name', 'StreamlabsCharity');
set('deploy_path', get('deploy_path'));

// Project repository
set('repository', 'git@github.com:twitchalerts/streamlabs-charity.git');

desc('Deploy your project');
task('deploy', function () {
    on(host('staging'), function () {
        if (test('[ -d {{deploy_path}} ]')) {
            within('{{deploy_path}}', function () {
                run('git checkout staging');
                run('git checkout .');
                runLocally('eval $(ssh-agent -s) && ssh-add ~/.ssh/id_rsa');
                set('test', 'ssh -T git@github.com');
                run('git pull');
            });
        } else {
            writeln(' run "eval $(ssh-agent -s) && ssh-add ~/.ssh/id_rsa" locally. Login to SSH and execute "ssh -T git@github.com"');
            runLocally('eval $(ssh-agent -s) && ssh-add ~/.ssh/id_rsa');
            within('{{deploy_path}}', function () {
                run('git clone {{repository}} ./', ['timeout' => null, 'tty' => true]);
                run('setup');
            });
        }
    });
});

desc('After deploy');
task('after_deploy', function () {
    within('{{deploy_path}}', function () {
        run('php artisan apidoc:generate');
        if (commandExist('composer')) {
            run('composer install');
        } else {
            run('sudo apt-get composer');
        }
    });
});

desc('After deploy');
task('setup', function () {
    within('{{deploy_path}}', function () {
        run("echo 'APP_NAME=
        APP_ENV=local
        APP_KEY=base64:MVDqA0Hak58y7Jr3IVS/lX4eOMR8+u6RCT8i/9EY89g=
        APP_DEBUG=true
        APP_URL=https://streamlabscharity.site
        APP_TIMEZONE=UTC
        
        LOG_CHANNEL=stack
        LOG_SLACK_WEBHOOK_URL=
        
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=streamlabscharity
        DB_USERNAME=homestead
        DB_PASSWORD=secret
        
        CACHE_DRIVER=file
        QUEUE_CONNECTION=sync' >> .env");
        run('php artisan apidoc:generate');
    });
});

after('deploy', 'after_deploy');
fail('deploy', 'deploy:unlock');
