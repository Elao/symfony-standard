# Elao Symfony Standard Edition

![Symfony 3.0](https://img.shields.io/badge/Symfony-3.0-blue.svg)
[![Join the chat at https://gitter.im/Elao/symfony-standard](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Elao/symfony-standard?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Requirements

* Make
* [VirtualBox >=5.0.20 <5.0.28](https://www.virtualbox.org/wiki/Downloads)
* [Vagrant 1.8.4](https://www.vagrantup.com/downloads.html)
* [Vagrant Landrush 1.0.0+](https://github.com/vagrant-landrush/landrush)

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

## Installation

Create the project and access to its directory (where [app] is your application name)

    $ composer create-project elao/symfony-standard [app] dev-master --prefer-dist --no-install
    $ cd [app]

Install and prepare the project dependencies

    $ make setup

## Customize

TODO

## Usage

Your app is accessible via [http://app.vendor.dev/app_dev.php](http://app.vendor.dev/app_dev.php)

## Faq

### VirtualBox DHCP Server

> A host only network interface you're attempting to configure via DHCP already
> has a conflicting host only adapter with DHCP enabled. The DHCP on this
> adapter is...

    $ VBoxManage dhcpserver remove --netname HostInterfaceNetworking-vboxnet0

### OSX DNS Cache

If you virtual machine does not answer, or ping to `127.0.53.53`

On Yosemite 10.10 to 10.10.3

    $ sudo discoveryutil mdnsflushcache
    $ sudo discoveryutil udnsflushcaches

Before Yosemite and on Yosemite 10.10.4

    $ sudo killall -HUP mDNSResponder

See: https://support.apple.com/kb/HT202516

### OSX ssh key forwarding

    $ ssh-add -K ~/.ssh/[your_private_key]

### Vagrant process crash

> An action '*foo*' was attempted on the machine '*bar*',
> but another process is already executing an action on the machine.
> Vagrant locks each machine for access by only one process at a time.
> Please wait until the other Vagrant process finishes modifying this
> machine, then try again.

Kill vagrant ruby process, and try again

    $ killall ruby

### Nfs shares without password confirmation

Edit /etc/sudoers

    $ sudo visudo

Under the section `# Cmnd alias specification`, add the following lines

    # Cmnd alias specification
    Cmnd_Alias VAGRANT_EXPORTS_ADD = /usr/bin/tee -a /etc/exports
    Cmnd_Alias VAGRANT_NFSD = /sbin/nfsd restart
    Cmnd_Alias VAGRANT_EXPORTS_REMOVE = /usr/bin/sed -E -e /*/ d -ibak /etc/exports

Then in `# User privilege specification` section, append the following line underneath `%admin  ALL=(ALL) ALL`

    %admin  ALL=(root) NOPASSWD: VAGRANT_EXPORTS_ADD, VAGRANT_NFSD, VAGRANT_EXPORTS_REMOVE
