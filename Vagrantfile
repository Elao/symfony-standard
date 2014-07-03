# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

options = {
  :name    => 'symfony-standard',
  :ip      => '172.16.1.6',
  :memory  => 512,
  :box     => 'debian-7-amd64',
  :aliases => []
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = options[:box]
  config.vm.box_url = 'https://boxes.elao.com/boxes/' + options[:box] + '.box'

  config.vm.hostname = options[:name] + '.dev'

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true

  if options[:aliases].any?
    config.hostmanager.aliases = ''

    for item in options[:aliases]
      config.hostmanager.aliases += item + '.' + config.vm.hostname + ' '
    end
  end

  config.vm.network :private_network, ip: options[:ip]

  config.ssh.forward_agent = true

  config.vm.synced_folder '.', '/home/vagrant/www',
    nfs: true,
    mount_options: ['nolock', 'actimeo=1']

  config.vm.provider :virtualbox do |vb|
    vb.name = options[:name]
    vb.customize ['modifyvm', :id, '--memory', options[:memory]]
    #vb.gui = true
  end

  if File.exists?(File.join(Dir.home, '.gitconfig')) then
    config.vm.provision 'shell',
      inline: "echo -e \"#{File.read("#{Dir.home}/.gitconfig")}\" > /home/vagrant/.gitconfig"
  end

  config.vm.provision 'ansible' do |ansible|
    ansible.playbook = 'app/vagrant/ansible/site.yml'
    ansible.inventory_path = 'app/vagrant/ansible/hosts'
    ansible.extra_vars = {host: options[:name] + '.dev'}
    #ansible.verbose = 'vvvv'
  end

end
