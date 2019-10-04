<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Module extends Command
{

    private $name;
    private $error;

    protected function configure()
    {
        $this
            ->setName('make:module')
            ->addArgument('name', InputArgument::REQUIRED)
            ->setDescription('Creates a new module.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->name = $input->getArgument('name');

        $this->_check_class();
        if (!empty($this->error) && $this->error != '') {
            $output->writeln("<error>" . $this->error . "</error>");
        } else {
            $this->createModule();
            $output->writeln("<info>'$this->name' Module created successfully!</info>");
        }
    }

    protected function createModule()
    {
        $prev_path = 'application/modules/';
        mkdir($prev_path.$this->name.'/');
        mkdir($prev_path.$this->name.'/controllers\/');
        mkdir($prev_path.$this->name.'/views\/');
$html='
<html>
    <head>
        <title>403 Forbidden</title>
    </head>
    <body>

    <p>Directory access is forbidden.</p>

    </body>
</html>';
        file_put_contents( $prev_path.$this->name.'/views\/index.html', trim($html));
    }

    private function _check_class()
    {
        
        if(!is_dir('application/modules/')){
            $this->error = 'Modules Folder doesn\'t exists!';
            return;
        }

        if(strstr($this->name, '/')){
            $this->error = 'Sub-Folder in module cannot be created!';
            return;
        }

        if (is_dir('application/modules/' . $this->name)) {
            $this->error = 'Module with this name already exists!';
            return;
        }

        return null;
    }
}
