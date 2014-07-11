# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

options = {
    :name   => 'symfony-standard',
    :memory => 512,
    :box    => 'elao/symfony-standard-debian',
    :debug  => false
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # Box
    config.vm.box = options[:box]

    # Hostname
    config.vm.hostname = options[:name] + '.dev'

    # Dns
    config.landrush.enabled            = true
    config.landrush.tld                = 'dev'
    config.landrush.guest_redirect_dns = false

    # Network
    config.vm.network 'private_network',
        type: 'dhcp'

    # Ssh
    config.ssh.forward_agent = true

    # Folders
    config.vm.synced_folder '.', '/home/vagrant/www',
        nfs: true,
        mount_options: ['nolock', 'actimeo=1']

    # Providers
    config.vm.provider :virtualbox do |vb|
        vb.name   = options[:name]
        vb.memory = options[:memory]
        vb.gui    = options[:debug]
        vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
    end

    # Cache
    if Vagrant.has_plugin?('vagrant-cachier')
        config.cache.scope = :box
    end

    # Git
    if File.exists?(File.join(Dir.home, '.gitconfig')) then
        config.vm.provision :file do |file|
            file.source      = '~/.gitconfig'
            file.destination = '/home/vagrant/.gitconfig'
        end
    end

    # Composer
    if File.exists?(File.join(Dir.home, '.composer/auth.json')) then
        config.vm.provision :file do |file|
            file.source      = '~/.composer/auth.json'
            file.destination = '/home/vagrant/.composer/auth.json'
        end
    end

    # Provisioners
    config.vm.provision 'ansible' do |ansible|
        ansible.playbook   = 'app/Resources/ansible/playbook.yml'
        ansible.extra_vars = {
            user: 'vagrant',
            host: options[:name] + '.dev'
        }
        ansible.verbose    = options[:debug] ? 'vvvv' : false
    end

end
