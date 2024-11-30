<?php
declare(strict_types=1);

namespace App\Services\Simulator;

use Symfony\Component\Console\Output\OutputInterface;

interface Simulator
{

    public function simulate(int $currentStep, OutputInterface $output) : void;

}