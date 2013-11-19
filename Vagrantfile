# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

options = {
  :namespace => 'elao',
  :name      => 'symfony',
  :ip        => '172.16.1.5',
  :memory    => 512,
  :box       => 'debian-7-amd64'
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box_url = 'http://www.elao.com/vagrant-boxes/' + options[:box] + '.box'

  config.vm.hostname = options[:name] + '.' + options[:namespace] + '.dev'
  
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true

  config.vm.network :private_network, ip: options[:ip]

  config.ssh.forward_agent = true

  config.vm.synced_folder '.', '/home/vagrant/www', nfs: true

  config.vm.provider :virtualbox do |vb|
      #vb.gui = true
      vb.name = options[:namespace] + '_' + options[:name]
   
      vb.customize ['modifyvm', :id, '--memory', options[:memory]]
    end
 
  config.vm.provision 'ansible' do |ansible|
    ansible.playbook = 'vagrant/ansible/site.yml'
    ansible.inventory_path = 'vagrant/ansible/hosts'
    ansible.extra_vars = {host: options[:name] + '.' + options[:namespace] + '.dev'}
    ansible.verbose = 'vvvv'
  end

end
