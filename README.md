Elao Symfony Standard Edition
========================

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

Host vagrant
  HostName 127.0.0.1
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
------

Installation :

* Change the project name inside the build.xml
