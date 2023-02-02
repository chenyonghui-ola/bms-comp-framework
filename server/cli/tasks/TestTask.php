<?php
/**
 * test
 *
 * php cli.php test -process hello
 */

namespace Imee\Cli\Tasks;

class TestTask extends CliApp
{
    public function mainAction(array $params = null)
    {
        $process = $params['process'] ?? '';

        switch ($process) {
            case 'hello':
                $this->hello();
                break;
            default:
                echo '不支持' . PHP_EOL;
        }
    }

    private function hello()
    {
        $this->debugInfo('======== begin ========');

        echo 'hello';

        $this->debugInfo('======== end ========');
    }

    private function debugInfo($str)
    {
        echo "[$str]::" . $this->debugMemConvert(memory_get_usage(true)) . date('Y-m-d H:i:s', time()) . PHP_EOL;
    }

    private function debugMemConvert($size): string
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . strtoupper($unit[$i]);
    }
}