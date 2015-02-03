# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

Vagrant.require_version ">= 1.6.5"

options = {
    :name        => 'symfony-standard',
    :vendor      => '',
    :aliases     => [],
    :memory      => 768,
    :box         => 'elao/symfony-standard-debian',
    :box_version => '~> 0.2.4',
    :folders     => {
        '.' => '/srv/symfony-standard/symfony'
    },
    :ansible     => 'ansible',
    :debug       => false
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # Box
    config.vm.box         = options[:box]
    config.vm.box_version = options[:box_version]

    # Hostname
    config.vm.hostname = options[:name] + ((options[:vendor] != '') ? '.' + options[:vendor] : '') + '.dev'

    # Hosts
    if Vagrant.has_plugin?('landrush')
        config.landrush.enabled            = true
        config.landrush.tld                = config.vm.hostname
        config.landrush.guest_redirect_dns = false
    elsif Vagrant.has_plugin?('vagrant-hostmanager')
        config.hostmanager.enabled     = true
        config.hostmanager.manage_host = true
        config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
            if vm.id
                `VBoxManage guestproperty get #{vm.id} "/VirtualBox/GuestInfo/Net/1/V4/IP"`.split()[1]
            end
        end
        if options[:aliases].any?
            config.hostmanager.aliases = ''
            for item in options[:aliases]
                config.hostmanager.aliases += item + '.' + config.vm.hostname + ' '
            end
        end
    end

    # Network
    config.vm.network 'private_network',
        type: 'dhcp'

    # Ssh
    config.ssh.forward_agent = true

    # Folders
    options[:folders].each do |host, guest|
        config.vm.synced_folder host, guest,
            type: 'nfs',
            mount_options: ['nolock', 'actimeo=1', 'fsc']
    end

    # Providers
    config.vm.provider :virtualbox do |vb|
        vb.name   = ((options[:vendor] != '') ? options[:vendor] + '_' : '') + options[:name]
        vb.memory = options[:memory]
        vb.gui    = options[:debug]
        vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
        vb.customize ['modifyvm', :id, '--natdnsproxy1', 'on']
    end

    # Cache
    if Vagrant.has_plugin?('vagrant-cachier')
        config.cache.scope = :box

        config.cache.synced_folder_opts = {
            type: :nfs,
            mount_options: ['rw', 'vers=3', 'tcp', 'nolock']
        }
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
        ansible.playbook   = options[:ansible] + '/playbook.yml'
        ansible.groups     = {
            'dev' => ['default']
        }
        ansible.verbose    = options[:debug] ? 'vvvv' : false
    end

end
