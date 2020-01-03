<?php   
        function debug($param){
        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }

    function is_in_array($array, $key, $value){
        $within_array = false;
        foreach( $array as $k=>$v ){
          if( is_array($v) ){
            $within_array = $this->is_in_array($v, $key, $value);
            if( $within_array){
                break;
            }
          } else {
            if($v == $value && $k == $key){
                $within_array = true;
                break;
            }
          }
        }
        return $within_array;
    }
?>  