<?php

use Goutte\Client;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', 600);
error_reporting(E_ALL);

include "vendor/autoload.php";

$hotelLocation = $_POST['inputLocation'];

$serverName="DESKTOP-GUJ1CRH";
$connectionInfo=array( "Database"=>"Ghoom-Lay");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn ){

        $query="insert into city_searches values('$hotelLocation','2019-06-08','2019-06-09')";

        $run = sqlsrv_query($conn,$query);
        sqlsrv_close($conn);
}


$urls[] = "https://www.booking.com/searchresults.en-gb.html?ss=$hotelLocation&offset=0&checkin_year=2019&checkin_month=6&checkin_monthday=8&checkout_year=2019&checkout_month=6&checkout_monthday=9";
$urls[] = "https://www.booking.com/searchresults.en-gb.html?ss=$hotelLocation&offset=20&checkin_year=2019&checkin_month=6&checkin_monthday=8&checkout_year=2019&checkout_month=6&checkout_monthday=9";
$urls[] = "https://www.booking.com/searchresults.en-gb.html?ss=$hotelLocation&offset=40&checkin_year=2019&checkin_month=6&checkin_monthday=8&checkout_year=2019&checkout_month=6&checkout_monthday=9";
$urls[] = "https://www.booking.com/searchresults.en-gb.html?ss=$hotelLocation&offset=80&checkin_year=2019&checkin_month=6&checkin_monthday=8&checkout_year=2019&checkout_month=6&checkout_monthday=9";

$client = new Client(); 

foreach ($urls as $url) {
        $crawler = $client->request('GET', $url);
        
        $crawler->filter('.sr_item')->each(function ($node) {
        
        preg_match_all('/<a aria-hidden="true" class="bicon-map-pin show_map map_address_pin" id="show_id(.*?)".*><\/a>/', $node->html(), $hotelId);
        $hotelId = $hotelId[1][0];
        
        preg_match_all('/<img class="hotel_image" src=".*" alt="(.*?)" .*>/', $node->html(), $hotelName);
        $hotelName = $hotelName[1][0];
        
        preg_match_all('/<img class="hotel_image" src="(.*?)" alt=".*" .*>/', $node->html(), $hotelImage);
        $hotelImage = $hotelImage[1][0];

        preg_match_all('/<div class="bui-review-score__badge">(.*?)<\/div>/', $node->html(), $hotelRating);   
        if(!empty($hotelRating[1][0])){
            $hotelRating = $hotelRating[1][0];
        }else{
            $hotelRating = 'Not Rated Yet!!';
        }

        preg_match_all('/<div class="bui-review-score__title"> (.*?) <\/div>/', $node->html(), $hotelReviewText);
        if(!empty($hotelReviewText[1][0])){
            $hotelReviewText = $hotelReviewText[1][0];
        }else{
            $hotelReviewText = 'Not Review Yet!!';
        }

        preg_match_all('/<div class="bui-review-score__title">.*">(.*?)<\/div>/', $node->html(), $hotelReview);
        if(!empty($hotelReview[1][0])){
            $hotelReview = $hotelReview[1][0];  
        }else{
            $hotelReview = 'Total Reviews = 0';
        }
        
        preg_match_all('/<i class="\s*bk-icon-wrapper\s*bk-icon-stars\s*star_track\s*".*title="(.*)">\s*(.*)\s*(.*)\s*<\/i>/', $node->html(), $hotelLevel);
        if(!empty($hotelLevel[1][0])){
            $hotelLevel = $hotelLevel[1][0];
        }else{
            $hotelLevel = 'Low Level Hotel';
        }

        preg_match_all('/<a class=" sr_item_photo_link sr_hotel_preview_track  " href="(.*?)from.*>\s*.*\s*height="200">\s*<span class="invisible_spoken">Opens in new window<\/span>\s*<\/a>/', $node->html(), $hotelURL);
        $hotelURL = $hotelURL[1][0];

        $HotelDescriptionURL = 'https://www.booking.com'.$hotelURL;
        
        $client = new Client();

        $crawler = $client->request('GET', $HotelDescriptionURL);

        preg_match_all('/<span class="bui-avatar-block__title">(.*?)<\/span>/', $crawler->html(), $UserReviewName);
        if(!empty($UserReviewName[1][0])){
                $UserReviewName = $UserReviewName[1][0];
        }else{
                $UserReviewName = "Atif";
        }

        preg_match_all('/<span class="c-review__body">"(.*?)"<\/span>/', $crawler->html(), $hotelReviewUserComment);
        if(!empty($hotelReviewUserComment[1][3])){
                $hotelReviewUserComment = $hotelReviewUserComment[1][3];
        }else{
                $hotelReviewUserComment = "“Reception staff specially Miss Almira very loving nice lady May God bless her very supportive and well behaved she is. All other staff ie room service was very nice too”";
        }

        preg_match_all('/<span class="\shp_address_subtitle\sjs-hp_address_subtitle\sjq_tooltip\s"\srel="14" data-source="top_link" data-coords=",".*".*\s.*>\s(.*?)\s<\/span>/', $crawler->html(), $hotelAddress);
        if(!empty($hotelAddress[1][0])){
                $hotelAddress = $hotelAddress[1][0];
        }else{
                $hotelAddress = " ";
        }
        

        $serverName="DESKTOP-GUJ1CRH";
        $connectionInfo=array( "Database"=>"Ghoom-Lay");
        $conn = sqlsrv_connect( $serverName, $connectionInfo);

        if( $conn ){
        $hotelLocation = $_POST['inputLocation'];
        
        $query="insert into hotel_data values($hotelId,'$hotelName',CAST('$hotelImage' AS VARBINARY(MAX)),'$hotelAddress','$hotelRating','$hotelReviewText','$hotelReview','$hotelLevel','1 night , 2 adult','8000','$UserReviewName','$hotelReviewUserComment','$HotelDescriptionURL','$hotelLocation')";
        
        $run = sqlsrv_query($conn,$query);
        sqlsrv_close($conn);
}


        echo 'Hotel Id =>   '.$hotelId;
        echo "<br>";
        echo 'Hotel Name =>   '.$hotelName;
        echo "<br>";
        echo 'Hotel Image =>   '.$hotelImage;
        echo "<br>";
        echo 'Hotel Rating =>   '.$hotelRating;
        echo "<br>";
        echo 'Hotel Review =>   '.$hotelReviewText;
        echo "<br>";
        echo 'Hotel Total Reviews =>   '.$hotelReview;
        echo "<br>";
        echo 'Hotel Type =>   '.$hotelLevel;
        echo "<br>";
        echo "Hotel Review User Comment=>   ".$UserReviewName;
        echo "<br>";
        echo "Hotel Review User Comment=>   ".$hotelReviewUserComment;
        echo "<br>";
        echo "Hotel Address=>   ".$hotelAddress;
        echo "<br>";
        echo "Hotel Description URL=>   ".$HotelDescriptionURL;
        echo "<br>";
        echo "<br>";

    });
}
?>