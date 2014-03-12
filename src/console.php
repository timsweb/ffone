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

$console->register('reset-password')
    ->setDefinition(array(
        new InputOption('username', 'u', InputOption::VALUE_REQUIRED, 'name'),
        new InputOption('password', 'p', InputOption::VALUE_REQUIRED, 'password'),
    ))
    ->setDescription('create a user for the fantasy f1l.')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
       $db        = $app['db']; /* @var $db \Doctrine\DBAL\Connection */
       $db->update('users', ['password' => password_hash($input->getOption('password'), PASSWORD_BCRYPT)], ['name' => $input->getOption('username')]);
       $output->writeln('Password is: ' . $input->getOption('password'));
    });

$console->register('bet-getevent')
    ->setDefinition(array(
        new InputOption('eventId', null, InputOption::VALUE_OPTIONAL, 'parentEventId'),
    ))
    ->setDescription('Get events under this event Id.')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $api = $app['bfClient']; /*@var $api \Betfair\Client*/
        $api->logon();
        $response = $api->getEvents($input->getOption('eventId'));
        $output->writeln(print_r($response, 1));
    });

$console->register('update-costs')
    ->setDescription('Test the API.')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $db = $app['db']; /*@var $db \Doctrine\DBAL\Connection*/
        $api = $app['bfClient']; /*@var $api \Fone\Betting\Client*/
        $api->logon();
        $driverCosts = [40, 39, 38, 37, 36, 33, 29, 28, 26, 25, 24, 22, 19, 16, 15, 14, 10, 9, 8, 7, 5, 3];
        $teamCosts = [40, 38, 36, 32, 30, 22, 18, 14, 11, 9, 7];
        $driverOdds = $api->getCurrentDriverOdds();
        $output->writeln('DRIVERS');
        $output->writeln('=======');

        foreach ($driverOdds as $driver => $odds) {
            $driver = trim($driver);
            if ($db->fetchColumn('select 1 from drivers where name = ?', [$driver])) {
                $cost = array_shift($driverCosts);
                $db->update('drivers', ['cost' => $cost], ['name' => $driver]);
                $output->writeln($driver . ' has cost ' . $cost);
            } else {
                $output->writeln('<error>' .$driver . ' not found</error>');
            }
        }

        $teamOdds = $api->getCurrentTeamOdds();
        $output->writeln(PHP_EOL . 'TEAMS');
        $output->writeln('=====');
        foreach($teamOdds as $t => $p) {
            $t = trim($t);
            if ($db->fetchColumn('select 1 from teams where name = ?', [$t])) {
                $cost = array_shift($teamCosts);
                $db->update('teams', ['cost' => $cost], ['name' => $t]);
                $output->writeln($t . ' has cost ' . $cost);
            } else {
                $output->writeln('<error>' .$t . ' not found</error>');
            }
        }
    });

$console->register('load-data')
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app) {
        $db = $app['db']; /*@var $db \Doctrine\DBAL\Connection*/
        $driverFile = fopen(__DIR__ . '/../resources/drivers.csv', 'r');
        while ($line = fgetcsv($driverFile)) {
            $line = array_map('trim', $line);
            $db->executeUpdate('REPLACE INTO drivers (code, name, team) VALUES (?, ?, ?)', $line);
        }
        fclose($driverFile);
        $teamsFile = fopen(__DIR__ . '/../resources/teams.csv', 'r');
        while ($line = fgetcsv($teamsFile)) {
            $line = array_map('trim', $line);
            $db->executeUpdate('REPLACE INTO teams (code, name) VALUES (?, ?)', $line);
        }
        fclose($teamsFile);
    });

$console->register('test')
        ->setCode(function(InputInterface $input, OutputInterface $output) use ($app) {
            $driverMapper = $app['driverMapper'];
            $hamilton = $driverMapper->get('HAM');
            $mercedes = $hamilton->getTeamModel();
            $output->writeln('Team: ' . $mercedes->getName());
    });

return $console;
