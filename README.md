Elao Symfony Standard Edition
=============================

Provide a pre-configured symfony standard edition, powered by vagrant with the following tools

 * Vim
 * Composer (global)
 * Oh-my-zsh
 * Git
 * Ant
 * NodeJs

Requirements
------------

 * [VirtualBox 4.3.2+](https://www.virtualbox.org/wiki/Downloads)
 * [Vagrant 1.3.5+](http://downloads.vagrantup.com/)
 * [Vagrant Host Manager plugin 1.2.3+](https://github.com/smdahlen/vagrant-hostmanager)
 * [ansible 1.4.0+](https://github.com/ansible/ansible) (installable via homebrew on osx)
 
Installation
------------
 Just clone the repository :)
 
 
Configuration
-------------

*"Mal nommer les choses, c'est ajouter au malheur du monde."* (Albert Camus)

Given three typical projects :

 * One simple without specific vendor : "foo"
 * One simple without vendor and funky name: "foo bAr 99 !"
 * One with vendor "gdf foo"

### Vagrant

In **Vagrantfile**, name option will be used as domain, suffixed by ".dev", so, lower case and hyphens only !

| Project       | Name       |
| ------------- |------------|
| foo           | foo        |
| foo bAr 99 !  | foo-bar-99 |
| gdf foo       | foo.gdf    |

Fill in the ip address (must be unique accross project), memory and box (currentlty limited to "debian-7-amd64" or "centos-6.4-x86_64")

### Composer

In **composer.json**, change the name and the description of the project

| Project       | Name                  | Description  |
| ------------- |-----------------------|--------------|
| foo           | foo/foo               | Foo          |
| foo bAr 99 !  | foo-bar-99/foo-bar-99 | Foo Bar 99 ! |
| gdf foo       | gdf/foo               | Gdf - Foo    |

### Symfony config files

In **app/config/parameters.yml.dist**, change the database name as following :

| Project       | Database name |
| ------------- |---------------|
| foo           | foo           |
| foo bAr 99 !  | foo-bar-99    |
| gdf foo       | gdf_foo       |


In **app/phpdoc.xml**, change the title as following :

| Project       | Title        |
| ------------- |--------------|
| foo           | Foo          |
| foo bAr 99 !  | Foo Bar 99 ! |
| gdf foo       | Gdf - Foo    |


In **app/config/config.yml**, change the session name as following :

| Project       | Session name |
| ------------- |--------------|
| foo           | foo          |
| foo bAr 99 !  | foo-bar-99   |
| gdf foo       | foo          |


### Behat

In **app/behat.yml**, change the base_url as following :

| Project       | Base url                           |
| ------------- |------------------------------------|
| foo           | http://foo.dev/app_test.php        |
| foo bAr 99 !  | http://foo-bar-99.dev/app_test.php |
| gdf foo       | http://foo.gdf.dev/app_test.php    |


### Ant


In **build.xml**, change the project name as following :

| Project       | Project name |
| ------------- |--------------|
| foo           | foo          |
| foo bAr 99 !  | foo-bar-99   |
| gdf foo       | foo.gdf      |


First run
---------

Once configured, 

```
$ vagrant up
$ vagrant ssh
$ cd www
$ composer update
```

Conventions
-----------

Bundles : FooBar/Bundle/CoreBundle

Bundles with vendor/namespace : Gdf/FooBar/Bundle/CoreBundle


Docblock
--------

Insert this doc block at the start of each php file

```
/**
 * This file is part of the [project] package.
 *
 * Copyright (C) 2013 [client]
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

Usage :

 * Just "vagrant up" in your current project dir. It will provision your vm and update /etc/hosts for both the host and the guest (using hostmanager plugin) the first run
 * If necessary, ssh to your vm with "vagrant ssh"
 * Remember to "vagrant halt" to shutdown your vm
 * If problems occurs, or if you feel the needs to, you can provision your vm with "vagrant provision" and update /etc/hosts with "vagrant hostmanager"
 * Simple, uh ?

Notes :

 * Project is mapped in ~/www in the vm
 * For speedup needings, cache and logs are respectively mapped in ~/cache and ~/logs
 
 
Jenkins
-------

See /app/jenkins.xml
