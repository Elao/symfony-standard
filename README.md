Elao Symfony Standard Edition
=============================

[![Join the chat at https://gitter.im/Elao/symfony-standard](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Elao/symfony-standard?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

> Disclamer: This project is still a work in progress, It work for us, but it's not well documented

Requirements
------------

 * [Vagrant 1.7.2+](http://www.vagrantup.com/downloads.html)
 * [VirtualBox 4.3.20+](https://www.virtualbox.org/wiki/Downloads)
 * [Ansible 1.7.2+](http://docs.ansible.com/intro_installation.html)
 * [Vagrant Landrush 0.15.2+](https://github.com/phinze/landrush) or [Vagrant Host Manager plugin 1.5.0+](https://github.com/smdahlen/vagrant-hostmanager)
 * [Vagrant Cachier](http://fgrehm.viewdocs.io/vagrant-cachier)

> Note: The $ stands for your machine CLI, while the ⇒ stands for the VM CLI

Installation
------------

Run the following command and follow the instructions

    $ composer create-project elao/symfony-standard my-project

Customize the project
---------------------

TODO

Running a project base on our standard application
--------------------------------------------------

    $ ansible-galaxy install -f -r ansible/playbook.roles
    $ vagrant up
    $ vagrant ssh
    ⇒ make install

> Note: Read the [FAQ](https://github.com/Elao/symfony-standard/wiki/FAQ) if you encouter the dhcp server error.

Your project is accessible via [http://your-project.your-vendor.dev/app_dev.php](http://your-project.your-vendor.dev/app_dev.php)

Running test in our application
-------------------------------

    $ vagrant ssh
    ⇒ make test

FAQ
---

Go to the [FAQ](https://github.com/Elao/symfony-standard/wiki/FAQ)

Install ElaoStrap (optionnal)
---

Install our custom twitter bootstrap Symfony bundle : [ElaoStrap](https://github.com/Elao/ElaoThemeElaoStrapBundle)
    
Update your /package.json. Here an example :

    {
      "name": "project-name",
      "author": {
        "name": "author-name",
        "email": "contact@your-domain.com"
      },
      "private": true,
      "napa": {},
      "dependencies": {
          "bootstrap-sass": "3.3.1",
          "elao-form.js": "git://github.com/elao/form.js",
          "elaostrap": "0.1.12",
          "dropify": "0.0.6",
          "fastclick": "1.0.3",
          "jquery": "2.1.1",
          "select2": "3.5.2-browserify",
          "font-awesome": "4.3.0"
      },
      "browser": {
          "bootstrap.transition": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/transition.js",
          "bootstrap.collapse": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/collapse.js",
          "bootstrap.tab": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/tab.js",
          "bootstrap.tooltip": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/tooltip.js",
          "bootstrap.popover": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/popover.js",
          "bootstrap.dropdown": "./node_modules/bootstrap-sass/assets/javascripts/bootstrap/dropdown.js",
          "select2.fr": "./node_modules/select2/select2_locale_fr.js",
          "dropify": "./node_modules/dropify/dist/js/dropify.js",
          "elaostrap.datepicker": "./node_modules/elaostrap/assets/js/vendors/datepicker.js",
          "elaostrap.simpleSelector": "./node_modules/elaostrap/assets/js/vendors/jquery.simple-selector.js",
          "elaostrap.fancybox": "./node_modules/elaostrap/assets/js/vendors/jquery.fancybox.pack.js",
          "elaostrap.easing": "./node_modules/elaostrap/assets/js/vendors/jquery.easing.js",
          "elaostrap.mobileNav": "./node_modules/elaostrap/assets/js/vendors/mobile-nav.js"
      },
      "browserify-shim": {
          "bootstrap.transition": {
              "depends": "jquery:jQuery"
          },
          "bootstrap.collapse": {
              "depends": [
                  "jquery:jQuery",
                  "bootstrap.transition"
              ]
          },
          "bootstrap.tab": {
              "depends": "jquery:jQuery"
          },
          "bootstrap.tooltip": {
              "depends": "jquery:jQuery"
          },
          "bootstrap.popover": {
              "depends": "jquery:jQuery"
          },
          "bootstrap.dropdown": {
              "depends": "jquery:jQuery"
          },
          "dropify": {
            "depends": "jquery:jQuery"
          }
      },
      "scripts": {
          "install": "napa"
      },
      "browserify": {
          "transform": ["browserify-shim"]
      },
      "devDependencies": {
          "gulp": "3.8.*",
          "del": "1.1.*",
          "elao-assets-gulp": "~0.1.16",
          "napa": "1.1.*",
          "browserify-shim": "3.8.*"
      }
    }
    
Update your /gulpfile.js, here an example :
    
    assets.config({
        (...),
        
        assets: {
            fonts: {
                groups: {
                    'elaostrap': {src: 'elaostrap/assets/fonts/**'},
                    'font-awesome': {src: 'font-awesome/fonts/**', dest: 'font-awesome'},
                    'dropify': {src: 'dropify/src/fonts/**', dest: 'dropify'},
                }
            }
        }
    });
    
Then do in your Vagrant VM :

    $ npm install
    
An example how to call the assets in your app/Resources/views/base.html.twig :
    
    <!DOCTYPE html>
    <html>
        <head>
            <title>{% block title %}My title{% endblock %}</title>
            <meta name="description" content="">
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
            <meta name="format-detection" content="telephone=no">
            {% block stylesheets %}
                <link rel="stylesheet" href="{{ asset('assets/css/web/main.css') }}">
            {% endblock %}
        </head>
        <body>
            {% block body %}{% endblock %}
    
            {% block javascripts %}
                <script src="{{ asset('assets/js/web/main.js') }}"></script>
            {% endblock %}
        </body>
    </html>

If you choose to create a  WebBundle to store assets, here an example of file structure and some code :
    
Create the file WebBundle/Resources/assets/js/web/main.js :

    var $              = require('jquery'),
        FastClick      = require('fastclick'),
        tab            = require('bootstrap.tab'),
        collapse       = require('bootstrap.collapse'),
        tooltip        = require('bootstrap.tooltip'),
        popover        = require('bootstrap.popover'),
        dropdown       = require('bootstrap.dropdown'),
        dropify        = require('dropify'),
        simpleSelector = require('elaostrap.simpleSelector'),
        fancybox       = require('elaostrap.fancybox'),
        easing         = require('elaostrap.easing'),
        datepicker     = require('elaostrap.datepicker'),
        select2        = require('select2');
    
    require('select2.fr');
    
    $(document).ready(function(){
        // your own code
    });
    
Create the file WebBundle/Resources/assets/sass/web/main.scss :

    /* import elaostrap css */
    @import "elaostrap/assets/sass/style";
    
    /* import your own css */
    @import "something/amazing";
    
Then do in your Vagrant VM :

    $ gulp
