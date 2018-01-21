<?php
namespace console\extensions\fileService;
use yii\console\Exception;

class TextDriver extends AbstractDriver
{
    /*
     * Modify file
     * @return bool
     */
    public function replaceData()
    {
        $obj = $this->wrapped->getObject();
        $preparePattern = sprintf($this->wrapped->pattern, $obj->search);
        $lines = function ($path){
            $buffer = fopen($path, 'r');
            if (!$buffer) throw new Exception();
            while ($line = fgets($buffer)) {
                yield $line;
            }
            fclose($buffer);
        };
        $newLInes = '';
        foreach ($lines($obj->file) as $key => $line) {
            $count = 0;
            $newLInes .= preg_replace($preparePattern, $obj->replace, $line, -1, $count);
            $this->wrapped->cntReplace += $count;
        }
        file_put_contents($obj->file, $newLInes);
        return true;
    }
}