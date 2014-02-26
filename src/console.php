<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('Fone Fantasy Legaue Admin', 'n/a');


$console
    ->register('import-calendar')
    ->setDefinition(array(
        new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'ICS File to import'),
    ))
    ->setDescription('Import an isc file into the database.')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $db        = $app['db']; /* @var $db \Doctrine\DBAL\Connection */
        $locaitons = [
            'Melbourne' => 'AU',
            'Kuala Lumpur' => 'MY',
            'Sakhir' => 'BH',
            'Shanghai' => 'CN',
            'Catalunya' => 'ES',
            'Monte Carlo' => 'MC',
            'Montreal' => 'CA',
            'Spielberg' => 'AT',
            'Silverstone' => 'GB',
            'Hockenheim' => 'DE',
            'Budapest' => 'HU',
            'Spa-Francorchamps' => 'BE',
            'Monza' => 'IT',
            'Singapore' => 'SG',
            'Suzuka' => 'JP',
            'Sochi' => 'RU',
            'Austin' => 'US',
            'Sao Paulo' => 'BR',
            'Yas Marina' => 'AE'
        ];
        $file      = fopen($input->getOption('file'), 'r');
        $rounds    = [];
        $buffer    = [];
        while ($line      = fgets($file)) {
            list($key, $val) = array_map('trim', explode(':', $line));
            $output->writeln($key . ':' . $val);
            switch ($key) {
                case 'DTSTART':
                    $buffer['racedate'] = strtotime($val);
                    break;
                case 'LOCATION':
                    $buffer['location'] = $val;
                    $buffer['countryCode'] = $locaitons[$val];
                    break;
                case 'SUMMARY':
                    $buffer['name']  = $val;
                    break;
                case 'END':
                    if ($val == 'VEVENT') {
                        $rounds[] = $buffer;
                        $buffer   = [];
                    }
                    break;
                default:
                //$output->write($key . ':' . $val);
            }
        }

        usort($rounds, function ($a, $b) {
            if ($a > $b) return 1;
            if ($a < $b) return -1;
            return 0;
        });

        foreach ($rounds as $r) {
            $db->insert('rounds', $r);
            $output->writeln($r['name'] . ' saved');
        }
    })
;

$console->register('create-user')
    ->setDefinition(array(
        new InputOption('username', 'u', InputOption::VALUE_REQUIRED, 'name'),
        new InputOption('password', 'p', InputOption::VALUE_REQUIRED, 'password'),
    ))
    ->setDescription('create a user for the fantasy f1l.')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
       $db        = $app['db']; /* @var $db \Doctrine\DBAL\Connection */
       $db->insert('users', ['name' => $input->getOption('username'), 'password' => password_hash($input->getOption('password'), PASSWORD_BCRYPT)]);
       $output->writeln('done');
    });
return $console;
