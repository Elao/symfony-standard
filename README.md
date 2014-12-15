Elao Symfony Standard Edition
=============================

Provide a pre-configured symfony standard edition, powered by vagrant with the following tools

 * Vim
 * Composer (global)
 * Oh-my-zsh
 * Git
 * Ant
 * NodeJs

What's included ?
-----------------

 * ant targets :
	 * test : run tests

What's customized ?
-------------------

 * Remove default /app/Resources/views
 * Add "symfony-standard" default session name in /app/config/config.yml
 * Add "doctrine.orm.naming_strategy.underscore" as doctrine orm naming strategy in /app/config/config.yml
 * Set validation api version to 2.5 in /app/config/config.yml
 * Add specific doctrine dbal dbname for test environment in /app/config/config_test.yml
 * Change default database_name from "symfony" to "symfony-standard" in /app/config/parameters.yml.dist
 * Don't use assetic controller in /app/config/parameters.yml.dist
 * Remove configurator dev route in /app/config/routing_dev.yml
 * Remove /web/config.php
 * Remove /web/apple-touch-icon.png
 * app_dev ip check disabled
 * Add /web/app_test.php
 * Add some common entries in .gitignore
 * Remove symfony standard stuff in /composer.json
 * Longer composer process timeout in /composer.json
 * Symlink as assets install option in /composer.json
 * Remove branch-alias option in /composer.json
 * Use symfony 3 directory layout
 * Disable assetic controller
 * Vagrant aware Symfony Kernel

Requirements
------------

 * [Vagrant 1.6.5+](http://www.vagrantup.com/downloads.html)
 * [VirtualBox 4.3.18+](https://www.virtualbox.org/wiki/Downloads)
 * [Ansible 1.7.2+](http://docs.ansible.com/intro_installation.html)
 * [Vagrant Landrush 0.15.2+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)

Installation
------------

Clone the project in your workspace

    $ git clone git@github.com:Elao/symfony-standard.git your-project
    $ cd your-project

Remove the .git file

    $ rm -rf .git

Install ansible playbook roles

    $ ansible-galaxy install -f -r ansible/playbook.roles

Change the name and the vendor of your project in the Vagrantfile options

```
    options = {
        :name        => 'your-project',
        :vendor      => 'your-vendor',
        ...
    }
```

Launch vagrant box and ssh into it

    $ vagrant up
    $ vagrant ssh

Note: Read the FAQ if you encouter the dhcp server error.

Initialize project

    ⇒ composer install
    ⇒ bin/console doctrine:schema:create

Initialize and build assets

    ⇒ npm install
    ⇒ bower install
    ⇒ gulp install

Note: The $ stands for your machine CLI, while the ⇒ stands for the VM CLI

Your project is accessible via [http://your-project.your-vendor.dev/app_dev.php](http://your-project.your-vendor.dev/app_dev.php)

Configuration
-------------

*"Mal nommer les choses, c'est ajouter au malheur du monde."* (Albert Camus)

Given three typical projects :

 * One simple without specific vendor : "foo"
 * One simple without vendor and funky name: "foo bAr 99 !"
 * One with vendor "vendor foo"

### Vagrant

In **Vagrantfile**, name option will be used as domain, suffixed by ".dev", so, lower case and hyphens only !

| Project       | Name       |
| ------------- |------------|
| foo           | foo        |
| foo bAr 99 !  | foo-bar-99 |
| vendor foo    | foo.vendor |

Fill in the ip address (must be unique accross project), memory and box (currentlty limited to "debian-7-amd64" or "centos-6.4-x86_64")

### Composer

In **composer.json**, change the name and the description of the project

| Project       | Name                  | Description  |
| ------------- |-----------------------|--------------|
| foo           | foo/foo               | Foo          |
| foo bAr 99 !  | foo-bar-99/foo-bar-99 | Foo Bar 99 ! |
| vendor foo    | vendor/foo            | Vendor - Foo |


### Symfony config files

In **app/config/parameters.yml.dist**, change the database name as following :

| Project       | Database name |
| ------------- |---------------|
| foo           | foo           |
| foo bAr 99 !  | foo-bar-99    |
| vendor foo    | vendor_foo    |


In **app/config/config.yml**, change the session name as following :

| Project       | Session name |
| ------------- |--------------|
| foo           | foo          |
| foo bAr 99 !  | foo-bar-99   |
| vendor foo    | foo          |


### Phpdoc

In **app/phpdoc.xml**, change the title as following :

| Project       | Title        |
| ------------- |--------------|
| foo           | Foo          |
| foo bAr 99 !  | Foo Bar 99 ! |
| vendor foo    | Vendor - Foo |


### Behat

In **app/behat.yml**, change the base_url as following :

| Project       | Base url                           |
| ------------- |------------------------------------|
| foo           | http://foo.dev/app_test.php        |
| foo bAr 99 !  | http://foo-bar-99.dev/app_test.php |
| vendor foo    | http://foo.vendor.dev/app_test.php |


### Ant

In **build.xml**, change the project name as following :

| Project       | Project name |
| ------------- |--------------|
| foo           | foo          |
| foo bAr 99 !  | foo-bar-99   |
| vendor foo    | foo.vendor   |

### Bower

In **bower.json**, change the project name as following :

| Project       | Project name |
| ------------- |--------------|
| foo           | foo          |
| foo bAr 99 !  | foo-bar-99   |
| vendor foo    | vendor/foo   |

Conventions
-----------

Bundles : FooBar/Bundle/CoreBundle

Bundles with vendor/namespace : Vendor/FooBar/Bundle/CoreBundle


Docblock
--------

Insert this doc block at the start of each php file

```
/**
 * This file is part of the [project] [package|bundle|...].
 *
 * Copyright (C) 2014 [vendor]
 *
 * @author Elao <contact@elao.com>
 */
```

Vagrant
-------

To get rid of ssh issues, just add this to your ~/.ssh/config:

```
Host 127.0.0.1
    Port 2222
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null
```

Allow ssh key forwarding in Mac OSX:

```
$ ssh-add -K ~/.ssh/[your_private_key]
```



##### Tired of typing your password required for the NFS shared folders synchronization, here's a *tip* for you.

Run the following command to edit /etc/sudoers

```
$ sudo visudo
```

Under the section `# Cmnd alias specification`, add the following lines

```
# Cmnd alias specification
Cmnd_Alias VAGRANT_EXPORTS_ADD = /usr/bin/tee -a /etc/exports
Cmnd_Alias VAGRANT_NFSD = /sbin/nfsd restart
Cmnd_Alias VAGRANT_EXPORTS_REMOVE = /usr/bin/sed -E -e /*/ d -ibak /etc/exports
```

Then in `# User privilege specification` section, append the following line underneath `%admin  ALL=(ALL) ALL`

```
%admin	ALL=(root) NOPASSWD: VAGRANT_EXPORTS_ADD, VAGRANT_NFSD, VAGRANT_EXPORTS_REMOVE
```


##### Notes :

 * Remember to "vagrant halt" to shutdown your vm
 * If problems occurs, or if you feel the needs to, you can provision your vm with "vagrant provision" and update /etc/hosts with "vagrant hostmanager"
 * Project is mapped in ~/www in the vm
 * For speedup needings, cache and logs are respectively mapped in ~/cache and ~/logs


Gulp
-----

To enable system notifications, install the related vagrant plugin:

```
vagrant plugin install vagrant-notify
```

Install also "terminal-notifier" application:

```
brew install terminal-notifier
```

Then, create the "notify-send" script, in /usr/local/bin, to make the glue:

```
#!/bin/bash
terminal-notifier -title "$2" -message "$3"
```

Now you can add the "--notify" parameter when using gulp.

Jenkins
-------

See /app/jenkins.xml

Mail Catcher
------------

A Mail Catcher server is available on the `1080` port on your project's domain:

http://your-project.your-vendor.dev:1080

FAQ
---

 * Receive this message: "A host only network interface you're attempting to configure via DHCP
already has a conflicting host only adapter with DHCP enabled. The
DHCP on this adapter is..."

```
VBoxManage dhcpserver remove --netname HostInterfaceNetworking-vboxnet0
```

 * Guest does not seem to answser. You can try to clear osx dns cache

```
sudo killall -HUP mDNSResponder
```
