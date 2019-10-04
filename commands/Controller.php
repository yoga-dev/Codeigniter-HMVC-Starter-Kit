<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Controller extends Command
{

	private $module;
	private $name;
	private $parent;
	private $error;

	protected function configure()
	{
		$this
			->setName('make:controller')
			->addArgument('name', InputArgument::REQUIRED)
			->addArgument('parent', InputArgument::OPTIONAL)
			->addOption(
                '--module',
                null,
                InputOption::VALUE_REQUIRED,
                'Enter module name?'
            )
			->setDescription('Creates a new controller.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->name = ucfirst($input->getArgument('name'));
		$this->parent = $input->getArgument('parent') == '' ? 'FRONT_Controller' : $input->getArgument('parent'); 
		$this->module = $input->getOption('module');

		$this->_check_class();
		if (!empty($this->error) && $this->error != '') {
			$output->writeln("<error>" . $this->error . "</error>");
		} else {
			$this->createController();
			$output->writeln("<info>'$this->name' controller created successfully!</info>");
		}
	}

	protected function createController()
	{

		$html = '
<?php 
defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');

class ' . $this->name . ' extends ' . $this->parent . ' {

	public function index()
	{
		# Your code 
	}

}';
$path=($this->module == '' ? 'application/controllers/' : 'application/modules/' . $this->module . '\/controllers/');
		return file_put_contents( $path . $this->name . '.php', trim($html));
	}

	private function _check_class()
	{
		if($this->module!='') {
			if(!is_dir('application/modules/' . $this->module)){
				$this->error = 'Module name doesn\'t exists!';
				return;
			}
			if (file_exists('application/modules/' . $this->module . '\/controllers/' . $this->name . '.php')) {
				$this->error = 'Class with this name already exists in module!';
				return;
			}
		}

		if (file_exists('application/controllers/' . $this->name . '.php')) {
			$this->error = 'Class with this name already exists!';
			return;
		}
		
		if (!file_exists('application/core/' . $this->parent . '.php')) {
			$this->error = 'Parent class with this name doesn\'t exists!';
		}

		return null;
	}
}
