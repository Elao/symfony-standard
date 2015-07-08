# {{ app_name }}

## Development

### Requirements

* [Vagrant 1.7.2+](http://www.vagrantup.com/downloads.html)
* [VirtualBox 4.3.28+](https://www.virtualbox.org/wiki/Downloads)
* [Ansible 1.9.1+](http://docs.ansible.com/intro_installation.html)
* [Vagrant Landrush 0.18.0+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)


### Installation

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

Install ansible playbook roles

    $ ansible-galaxy install -r ansible/roles.yml -p ansible/roles -f

Launch vagrant box, and ssh into it

    $ vagrant up
    $ vagrant ssh

Initialize project

    ⇒ composer install
    ⇒ bin/console doctrine:database:create
    ⇒ bin/console doctrine:schema:create

Initialize and build assets

    ⇒ npm install
    ⇒ gulp install

You should access the project via [http://{{ app_host }}.dev/app_dev.php](http://{{ app_host }}.dev/app_dev.php)

### Usage

    * Supervisor:  http://{{ app_host }}.dev:9001
    * Mailcatcher: http://{{ app_host }}.dev:1080
    * Log.io:      http://{{ app_host }}.dev:28778
    * phpMyAdmin:  http://{{ app_host }}.dev:1979

Enable/Disable php xdebug

    ⇒ elao_php_xdebug [on|off]
