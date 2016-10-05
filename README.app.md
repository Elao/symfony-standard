# App

## Development

> Note: The `$` stands for your machine CLI, while the `⇒` stands for the VM CLI

### Requirements

* Make
* [VirtualBox 5.0.20+](https://www.virtualbox.org/wiki/Downloads)
* [Vagrant 1.8.4+](https://www.vagrantup.com/downloads.html)
* [Vagrant Landrush 1.0.0+](https://github.com/vagrant-landrush/landrush)

### Setup

Clone the project in your workspace, and launch setup

    $ make setup

You should access the project via http://app.dev/app_dev.php

### Usage

Start/Stop/Ssh

    $ vagrant up/halt/ssh

Build

    ⇒ make build

Admin

* [MailHog](http://app.dev:8025)
* [Supervisor](http://app.dev:9001)
* [RTail](http://app.dev:8888)
* [OPcache Dashboard](http://app.dev:2013)
* [PhpMyAdmin](http://app.dev:1979)
* [PhpPgAdmin](http://app.dev:1980)
* [PhpRedisAdmin](http://app.dev:1981)
* [MongoExpress](http://app.dev:8081)
* [Elasticsearch](http://app.dev:9200/_plugin/head/)
* [Ngrok](http://app.dev:4040)
* [InfluxDB](http://app.dev:8083)
