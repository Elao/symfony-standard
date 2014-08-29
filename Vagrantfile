# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

Vagrant.require_version ">= 1.6.3"

options = {
    :name    => 'symfony-standard',
    :vendor  => '',
    :memory  => 768,
    :box     => 'elao/symfony-standard-debian',
    :folder  => '.',
    :ansible => 'app/Resources/ansible',
    :debug   => false
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    # Box
    config.vm.box = options[:box]

    # Hostname
    config.vm.hostname = options[:name] + ('.' + options[:vendor] if options[:vendor]) + '.dev'

    # Hosts
    if Vagrant.has_plugin?('landrush')
        config.landrush.enabled            = true
        config.landrush.tld                = 'dev'
        config.landrush.guest_redirect_dns = false
    elsif Vagrant.has_plugin?('vagrant-hostmanager')
        config.hostmanager.enabled     = true
        config.hostmanager.manage_host = true
        config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
            if vm.id
                `VBoxManage guestproperty get #{vm.id} "/VirtualBox/GuestInfo/Net/1/V4/IP"`.split()[1]
            end
        end
    end

    # Network
    config.vm.network 'private_network',
        type: 'dhcp'

    # Ssh
    config.ssh.forward_agent = true

    # Folders
    config.vm.synced_folder options[:folder], '/srv/www',
        type: 'nfs',
        mount_options: ['nolock', 'actimeo=1', 'fsc']

    # Providers
    config.vm.provider :virtualbox do |vb|
        vb.name   = (options[:vendor] + '_' if options[:vendor]) + options[:name]
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
        ansible.extra_vars = {
            user: 'vagrant',
            host: options[:name] + ('.' + options[:vendor] if options[:vendor]) + '.dev'
        }
        ansible.verbose    = options[:debug] ? 'vvvv' : false
    end

end
