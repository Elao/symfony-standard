# Elao Symfony Standard Edition

[![Join the chat at https://gitter.im/Elao/symfony-standard](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Elao/symfony-standard?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

> Disclamer: This project is still a work in progress, It work for us, but it's not well documented

## Requirements

* [Vagrant 1.7.2+](http://www.vagrantup.com/downloads.html)
* [VirtualBox 4.3.28+](https://www.virtualbox.org/wiki/Downloads)
* [Ansible 1.9.2+](http://docs.ansible.com/intro_installation.html)
* [Vagrant Landrush 0.18.0+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

### Installation

Run the following command and follow the instructions

    $ composer create-project elao/symfony-standard my-app dev-master

### Customize

TODO

### Running a project base on our standard application


    $ ansible-galaxy install -r ansible/roles.yml -p ansible/roles -f
    $ vagrant up
    $ vagrant ssh
    ⇒ make install

> Note: Read the [FAQ](https://github.com/Elao/symfony-standard/wiki/FAQ) if you encouter the dhcp server error.

Your app is accessible via [http://your-app.your-vendor.dev/app_dev.php](http://your-app.your-vendor.dev/app_dev.php)

Running test in our application
-------------------------------

    $ vagrant ssh
    ⇒ make test

FAQ
---

Go to the [FAQ](https://github.com/Elao/symfony-standard/wiki/FAQ)
