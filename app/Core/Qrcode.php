<?php

namespace Padchat\Core;

use PHPQRCode\QRcode as QrCodeConsole;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Qrcode
{

    /**
     * show qrCode on console.
     * @param $text
     */
    public function show($text)
    {
        $output = new ConsoleOutput();
        static::initQrcodeStyle($output);
        $pxMap[0] = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '<whitec>mm</whitec>' : '<whitec>  </whitec>';
        $pxMap[1] = '<blackc>  </blackc>';
        $text = QrCodeConsole::text($text);
        $length = strlen($text[0]);
        $output->write("\n");
        foreach ($text as $line) {
            $output->write($pxMap[0]);
            for ($i = 0; $i < $length; $i++) {
                $type = substr($line, $i, 1);
                $output->write($pxMap[$type]);
            }
            $output->writeln($pxMap[0]);
        }
    }
    /**
     * init qrCode style.
     *
     * @param OutputInterface $output
     */
    private static function initQrcodeStyle(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('black', 'black', ['bold']);
        $output->getFormatter()->setStyle('blackc', $style);
        $style = new OutputFormatterStyle('white', 'white', ['bold']);
        $output->getFormatter()->setStyle('whitec', $style);
    }
}