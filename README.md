Elao Symfony Standard Edition
=============================

Requirements
------------

 * [Vagrant 1.6.5+](http://www.vagrantup.com/downloads.html)
 * [VirtualBox 4.3.20+](https://www.virtualbox.org/wiki/Downloads)
 * [Ansible 1.7.2+](http://docs.ansible.com/intro_installation.html)
 * [Vagrant Landrush 0.15.2+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)

Installation
------------

Run the following command and follow the instructions

    composer create-project elao/symfony-standard my-project

Customize the project
---------------------

Running a project base on our standard application
--------------------------------------------------

    $ ansible-galaxy install -f -r ansible/playbook.roles
    $ vagrant up
    $ vagrant ssh
    ⇒ make install

> Note:
> - Read the FAQ if you encouter the dhcp server error.
> - The $ stands for your machine CLI, while the ⇒ stands for the VM CLI

Your project is accessible via [http://your-project.your-vendor.dev/app_dev.php](http://your-project.your-vendor.dev/app_dev.php)

Running test in our application
-------------------------------

    $ vagrant ssh
    ⇒ make test

FAQ
---

### Issue with the VirtualBox DHCP Server

Receive this message: "A host only network interface you're attempting to configure via DHCP
already has a conflicting host only adapter with DHCP enabled. The
DHCP on this adapter is..."

```
VBoxManage dhcpserver remove --netname HostInterfaceNetworking-vboxnet0
```

### Issue with the OSX DNS Server

Guest does not seem to answser. You can try to clear osx dns cache (before yosemite)

    sudo killall -HUP mDNSResponder

Guest does not seem to answser (in yosemite). If you ping and get `127.0.53.53`

    sudo discoveryutil mdnsflushcache
    sudo discoveryutil udnsflushcaches


### SSH Issues

To get rid of ssh issues, just add this to your ~/.ssh/config:

    Host 127.0.0.1
        Port 2222
        StrictHostKeyChecking no
        UserKnownHostsFile=/dev/null

Allow ssh key forwarding in Mac OSX:

    $ ssh-add -K ~/.ssh/[your_private_key]

### Tired of typing your password required for the NFS shared folders synchronization, here's a *tip* for you.

Run the following command to edit /etc/sudoers

    $ sudo visudo

Under the section `# Cmnd alias specification`, add the following lines

    # Cmnd alias specification
    Cmnd_Alias VAGRANT_EXPORTS_ADD = /usr/bin/tee -a /etc/exports
    Cmnd_Alias VAGRANT_NFSD = /sbin/nfsd restart
    Cmnd_Alias VAGRANT_EXPORTS_REMOVE = /usr/bin/sed -E -e /*/ d -ibak /etc/exports

Then in `# User privilege specification` section, append the following line underneath `%admin  ALL=(ALL) ALL`

    %admin	ALL=(root) NOPASSWD: VAGRANT_EXPORTS_ADD, VAGRANT_NFSD, VAGRANT_EXPORTS_REMOVE
