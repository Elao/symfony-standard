# {{ app.label }}

## Development

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

### Requirements

* Make
* [VirtualBox 5.0.20+](https://www.virtualbox.org/wiki/Downloads)
* [Vagrant 1.8.2+](https://www.vagrantup.com/downloads.html)
* [Vagrant Landrush 1.0.0+](https://github.com/vagrant-landrush/landrush)

### Setup

Clone the project in your workspace, and launch setup

    $ make setup

You should access the project via http://{{ app.name }}.dev/app_dev.php

### Usage

Start/Stop/Ssh

    $ vagrant up/halt/ssh

Build

    ⇒ make build

Admin

* [MailHog](http://{{ app.name }}.dev:8025)
* [Supervisor](http://{{ app.name }}.dev:9001)
* [RTail](http://{{ app.name }}.dev:8888)
* [OPcache Dashboard](http://{{ app.name }}.dev:2013)
* [PhpMyAdmin](http://{{ app.name }}.dev:1979)
* [Elasticsearch](http://{{ app.name }}.dev:9200/_plugin/dejaVu)
* [Ngrok](http://{{ app.name }}.dev:4040)
