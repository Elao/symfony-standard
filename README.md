Elao Symfony Standard Edition
========================

Naming
------

Lower case and hyphens only !
Ex : "foo", "foo-bar"

If you have a vendor/namespace, such as "gdf" or "arianespace", appends it with a dot.
Ex : "foo-bar.gdf"

For db names, just reverse name and vendor/namespace, replacing the dot by an underscore
Ex : "gdf_foo-bar"

Bundles : FooBar/Bundle/CoreBundle
Bundles with vendor/namespace : Gdf/FooBar/Bundle/CoreBundle

Vagrant
-------

Requirements :

 * VirtualBox 4.3.2+
 * Vagrant 1.3.5+
 * Vagrant Host Manager plugin 1.2.3+
 * ansible 1.3.4+

Installation :

 * Fill options in Vagrantfile. Remember to replace the ip by an actual one, and to have only letters, numbers, hyphens or dots in name. From now you can only choose "debian-7-amd64" or "centos-6.4-x86_64" as box
 * To get rid of ssh issues, just add this to your ~/.ssh/config:

Host 127.0.0.1
    Port 2222
    StrictHostKeyChecking no
    UserKnownHostsFile=/dev/null

Usage :

 * Just "vagrant up" in your current project dir. It will provision your vm and update /etc/hosts for both the host and the guest (using hostmanager plugin) the first run
 * If necessary, ssh to your vm with "vagrant ssh"
 * Remember to "vagrant halt" to shutdown your vm
 * If problems occurs, or if you feel the needs to, you can provision your vm with "vagrant provision" and update /etc/hosts with "vagrant hostmanager"
 * Simple, uh ?

Notes :

 * Project is mapped in ~/www in the vm
 * For speedup needings, cache and logs are respectively mapped in ~/cache and ~/logs

Behat
------

Installation :

 * Change the base url inside app/behat.yml according to VagrantFile config.vm.hostname

Ant
---

Installation :

 * Change project name in build.xml
 * Change project title in app/phpdoc.xml

Checklist
---------

 * Change session name in app/config/config.yml :

framework:
    ...
    session:
        name: foo-bar
