<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Model extends Command
{
    private $name;
    private $parent;
    private $table;
    private $error;

    protected function configure()
    {
        $this
            ->setName('make:model')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('parent', InputArgument::OPTIONAL)
            ->addOption(
                '--table',
                null,
                InputOption::VALUE_REQUIRED,
                'Enter table name?'
            )
            ->setDescription('Creates a new model.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->name = ucfirst($input->getArgument('name'));
        $this->parent = $input->getArgument('parent') == '' ? 'MY_Model' : $input->getArgument('parent');
        $this->table = $input->getOption('table') == '' ? '' : PHP_EOL.'protected $table = \''.$input->getOption('table').'\';'.PHP_EOL;

        $this->_check_class();
        if (!empty($this->error) && $this->error != '') {
            $output->writeln('<error>'.$this->error.'</error>');
        } else {
            $this->createController();
            passthru($this->serverCommand());
            $output->writeln("<info>'$this->name' model created successfully!</info>");
        }
    }

    protected function resolvePath($path, $html)
    {
        $prev_path = 'application/models/';
        if (strpos($path, '/')) {
            $folder = explode('/', $path);

            for ($i = 0; $i < count($folder); $i++) {
                if ($i == count($folder) - 1) {
                    return file_put_contents($prev_path.ucwords($folder[$i]).'.php', trim($html));
                } else {
                    $prev_path = $prev_path.$folder[$i].'/';
                    if (!file_exists($prev_path)) {
                        mkdir($prev_path);
                    }
                }
            }
        } else {
            return file_put_contents($prev_path.ucwords($path).'.php', trim($html));
        }
    }

    protected function serverCommand()
    {
        return sprintf(
            '%s %s',
            'composer',
            'dump-autoload'
        );
    }

    protected function createController()
    {
        $folder = explode('/', $this->name);
        $html = '
<?php 
defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');

class '.ucwords($folder[count($folder) - 1]).' extends '.$this->parent.' {
'.$this->table.'
    public function __construct(){
        parent::__construct();
    }

}';
        $this->resolvePath($this->name, $html);

        return $this->serverCommand();
    }

    private function _check_class()
    {
        if (file_exists('application/models/'.$this->name.'.php')) {
            $this->error = 'Class name already exists!';

            return;
        }

        if (!file_exists('application/core/'.$this->parent.'.php')) {
            $this->error = 'Parent class name doesn\'t exists!';
        }
    }
}
