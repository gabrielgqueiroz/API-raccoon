<?php
class DataProcessing{
    
    private $url = "https://us-central1-psel-clt-ti-junho-2019.cloudfunctions.net/psel_2019_get";
    private $url_error = "https://us-central1-psel-clt-ti-junho-2019.cloudfunctions.net/psel_2019_get_error";
    
    //retorna um array associativo
    private function getData($url){
        $ch = curl_init($url);                                                                   
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                                                                    
        $result =curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        return $result;

    } 

    public function orderByPrice_promocao(){
        $result = $this->getData($this->url);
        $result['posts'] = array_merge( array_values( $result['posts'])  , array_values( $result['posts']));
        $result['posts'] = array_merge( array_values( $result['posts'])  , array_values( $result['posts']));
        $result['posts'] = array_merge( array_values( $result['posts'])  , array_values( $result['posts']));
        $result['posts'] = array_merge( array_values( $result['posts'])  , array_values( $result['posts']));
        $sorted = array();

        list($usec, $sec) = explode(' ', microtime());
        $script_start = (float) $sec + (float) $usec;

        foreach($result['posts'] as $key => $value){
            $title = $result['posts'][$key]['title'];
            $title = explode('_' , $title);
            if (in_array("promocao", $title)){
                //if(!($this->is_in_array($sorted, 'product_id', $result['posts'][$key]['product_id']))){
                if(!($this->findEqualValues($sorted, 'product_id', $result['posts'][$key]['product_id']))){
                    $aux= sizeof($sorted);
                    $sorted[$aux]['product_id'] = $result['posts'][$key]['product_id'];
                    $sorted[$aux]['price_field'] = $result['posts'][$key]['price'];
                }
            }
        } 

        list($usec, $sec) = explode(' ', microtime());
        $script_end = (float) $sec + (float) $usec;
        $elapsed_time = round($script_end - $script_start, 5);
        echo 'Elapsed time: ', $elapsed_time, ' secs. Memory usage: ', round(((memory_get_peak_usage(true) / 1024) / 1024), 2), 'Mb';

        usort($sorted, array('DataProcessing', 'cmp'));
        return $sorted;
    }

    public function orderByPrice_like(){
        $result = $this->getData($this->url);
        $sorted = array();
        foreach($result['posts'] as $key => $value){
            if ($result['posts'][$key]['media'] == 'instagram_cpc' && $result['posts'][$key]['likes'] > 700){                
                $aux= sizeof($sorted);
                $sorted[$aux]['post_id'] = $result['posts'][$key]['post_id'];
                $sorted[$aux]['price_field'] = $result['posts'][$key]['price'];
            }
        } 
        usort($sorted, array('DataProcessing', 'cmp'));
        return $sorted; 
    }

    public function likesInMonth($mes, $ano){
        $result = $this->getData($this->url);
        $sum_like= 0;
        $i = 0;
        foreach($result['posts'] as $key => $value){
            $date = $result['posts'][$key]['date'];
            $date = explode('/' , $date);
            if($result['posts'][$key]['media'] == 'instagram_cpc' || $result['posts'][$key]['media'] == 'google_cpc' || $result['posts'][$key]['media'] == 'facebook_cpc' ){
                if($date[1] == $mes && $date[2] == $ano){
                    $sum_like += $result['posts'][$key]['likes'];
                }
            }
        }
        return $sum_like;
    }

    public function getError(){
        $result = $this->getData($this->url_error);
        $prod_id= array();
        $error_ids= array();
        foreach ($result['posts'] as $key => $value) {
            if(!(in_array($result['posts'][$key]['product_id'], $prod_id))){
                array_push($prod_id, $result['posts'][$key]['product_id']);
                for ($i=$key ; $i < sizeof($result['posts']) ; $i++) {
                    if ($result['posts'][$key]['product_id'] == $result['posts'][$i]['product_id'] && $result['posts'][$key]['price'] != $result['posts'][$i]['price']){
                        array_push($error_ids, $result['posts'][$key]['product_id']);
                        break;
                    }
                }
                
            }
        }
        sort($error_ids);
        return $error_ids;
    }

    private function is_in_array($array, $key, $value){
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

    private function cmp($a, $b){   
        if($a['price_field'] - $b['price_field'] == 0 ){
            if (isset($a['product_id']))
                return $a['product_id'] > $b['product_id'];
            else
                return $a['post_id'] > $b['post_id'];
        } else{
            return $a['price_field'] - $b['price_field'];
        }
    }   

    private function findEqualValues($array, $key, $value ){
        foreach ($array as $k => $v) {
            if ($array[$k][$key] == $value){
                return true;
            }
        }
        return false;
    }
}

?>



