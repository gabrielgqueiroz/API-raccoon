<?php   
        include_once ('util.php');    
        include_once ('raccoonPosterData.php');
        $data = new DataProcessing();


        $json_response = array( 'full_name'=> "Gabriel Gomes Queiroz",
                                'email' => "ggabriel12.gg@gmail.com",
                                'code_link'=> "https://github.com/gabrielgqueiroz/psel-raccoon/",
                                'response_a' => $data->orderByPrice_promocao(),
                                'response_b' => $data-> orderByPrice_like(),
                                'response_c' => $data-> likesInMonth( 5, 2019 ),
                                'response_d' => $data->getError());
        debug($json_response);
        $json_response = json_encode($json_response );
        //echo $json_response;

        $url = "";

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_POSTFIELDS, $json_response);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);
    


        
?>