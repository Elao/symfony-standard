# {{ app_name }}

## Development

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

### Requirements

* [Vagrant 1.7.2+](http://www.vagrantup.com/downloads.html)
* [VirtualBox 4.3.28+](https://www.virtualbox.org/wiki/Downloads)
* [Ansible 1.9.1+](http://docs.ansible.com/intro_installation.html)
* [Vagrant Landrush 0.18.0+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)

### Setup

Clone the project in your workspace, and launch setup

    $ make setup

You should access the project via http://{{ app_host }}.dev/app_dev.php

### Usage

Launch vagrant box, and ssh into it

    $ vagrant up
    $ vagrant ssh

Build assets

    ⇒ gulp

Enable/Disable php xdebug

    ⇒ elao_php_xdebug [on|off]

* *Supervisor*: http://clap-digital.tcs.dev:9001
* *Mailcatcher*: http://clap-digital.tcs.dev:1080
* *Log.io*: http://clap-digital.tcs.dev:28778
* *phpMyAdmin*: http://clap-digital.tcs.dev:1979
