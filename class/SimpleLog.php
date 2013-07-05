<?php
/**
 * Class SimpleLog
 *
 * @property array logs Tututlan loglar
 * @property bool  trace Log'un kayıt anında ekrana yazdırılıp yazdırılmayacağı
 */
class SimpleLog extends Component implements Log{
    public $logs = array();
    public $trace = false;

    public function add($type,$value)
    {
        $newLog = array('type'=>$type,'message'=>$value,'time'=>microtime(true));
        $this->logs[] = $newLog;

        if($this->trace)
            echo '<p>'.$newLog['type'] . ' | ' . $newLog['message'] .' | ' . $newLog['time'] . '</p>';
    }

    public function getLogs(){
        return $this->logs;
    }
}