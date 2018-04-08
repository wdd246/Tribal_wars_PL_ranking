<?php
include('ini.php');

// connecting to DB
$con = @new mysqli($host, $user, $pass, $db); 
if (mysqli_connect_errno() != 0){
	echo '<p>Wystąpił błąd połączenia: ' . mysqli_connect_error() . '</p>';
}

error_reporting(0);
ini_set('display_errors', 0);

$s=1; //first world
$swiaty=128; //last world

while($s<=$swiaty){ //geting information from each world
    
    $url = "http://pl.twstats.com/pl{$s}/index.php?page=rankings&mode=tribes"; //Getting tribe info from ranking
    $content = file_get_contents($url);
    $start_trbe = explode( '<tr class="r1">' , $content );
    $end_tribe = explode('</td>' , $start_trbe[1] );
    
    $tribe="http://pl.twstats.com/pl{$s}/"; //link to each world
    $t_id=""; //string tribe ID
    $t_count=""; //string count of members
    $t_points=""; //string points trib
    $t_name=""; //string t_name tribe
    $p_name=""; //string player t_name
    $p_points=""; //string player points
    $p_villages=""; //string player villages


    // GETING TRIBE INFO 
    for($i=1;$i<7;$i++){
        echo $end_tribe[$i]."<br>";
        if($i==1){ // first <td> with link,id,t_name
            for($j=14;$j<=100;$j++){ // only for echos informations
                if($end_tribe[1][$j] == '"') break;
                $tribe.=$end_tribe[1][$j];
            }
            for($j=42;$j<=100;$j++){ //getting string of tribe id
                if($end_tribe[1][$j] == '"') break;
                $t_id.=$end_tribe[1][$j];
            }
            for($j=42;$j<=100;$j++){ //getting t_name of tribe
                if($t_id[$j-42]==$end_tribe[1][$j]) continue;
                if($end_tribe[1][$j]=='"' ||$end_tribe[1][$j]=='>' ) continue;
                if($end_tribe[1][$j]=='<') break;
                $t_name.=$end_tribe[1][$j];
            }
            $id[$s]=$t_id; // insert tribe id to array 
        
        }
        if($i==4){ //4th <td> with numbers of players
            for($j=5;$j<=7;$j++){ //getting string with count of players
                if($end_tribe[4][$j] == '') break;
                $t_count.=$end_tribe[4][$j];
            }
        }
        if($i==3){ //3th <td> with tribe points
            for($j=5;$j<=19;$j++){ //geting string of tribe points
                if($end_tribe[3][$j] == '') break;
                $t_points.=$end_tribe[3][$j];
            }
        }
    }
    
    intval($t_count); //int count of members
    
    $t_points=str_replace(',','',$t_points);
    intval($t_points); //int points
    $points_t[$s]=intval($t_points); //int points in array
    //echo $points_t[$s];
    
    $t_memb[$s]=intval($t_count); //int count mmebers
    echo $tribe."<br>";
    //echo $id[$s];
    
    
//    $insert_tribe = "INSERT INTO tribal(id, t_name, count, points, wins, world) VALUES ($id[$s], '" . mysqli_real_escape_string($con,$t_name) . "' ,$t_count,$points_t[$s],1,$s)";
//    
//    $nn = "UPDATE tribal SET t_name = '" . mysqli_real_escape_string($con,$t_name) . "' WHERE world = $s";
//    if ($con->query($nn) === TRUE) {
//        echo "New record created successfully";
//    } else {
//        echo "Error: " . $nn . "<br>" . $con->error;
//    }
    


    echo "<br>";   
    
    $member_url= "http://pl.twstats.com/pl{$s}/index.php?page=tribe&mode=members&id={$id[$s]}"; //Getting player info from ranking
    $member_content = file_get_contents($member_url);
    $start_member = explode( '<tr class="r1">' , $member_content );

    // GETTING PLAYER INFO
    for($l=1;$l<=$t_memb[$s]/2;$l++){ // info from each playrer from each tribe
        
        $end_member = explode('</tr>' , $start_member[$l] );
        
        for($k=0;$k<2;$k++){ //info from each <td> in <tr>
            
            echo "<br>"; 
            if($l==$t_memb[$s]/2){ //last 2 members without footer included in <td>
                // FIRST LAST MEMBER
                
                echo $end_member[0];
                echo "<br>";
                echo $end_member[1];
                echo "<br>";
                //echo $end_member[2];
                
                for($m=73;$m<=110;$m++){ //getting player id string
                    if($end_member[0][$m]=='i' ||$end_member[0][$m]=='d' ||$end_member[0][$m]=='='  )continue;
                    if($end_member[0][$m]=='"')break;
                    $player_t_id.=$end_member[0][$m];
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+1;$m<=150;$m++){ //getting player name
                    if($end_member[0][$m]=='>' ||$end_member[0][$m]=='"' ||$end_member[0][$m]=='='  )continue;
                    if($end_member[0][$m]=='<')break;
                    $p_name.=$end_member[0][$m]; 
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+strlen($p_name)+16;$m<=300;$m++){ //getting player points
                    if($end_member[0][$m]=='>' ||$end_member[0][$m]=='"' ||$end_member[0][$m]=='='  )continue;
                    if($end_member[0][$m]=='<')break;
                    $p_points.=$end_member[0][$m]; 
                    //echo $p_points;
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+strlen($p_name)+strlen($p_points)+26;$m<=300;$m++){ //getting player count of villages
                    if($end_member[0][$m]=='>' ||$end_member[0][$m]=='"' ||$end_member[0][$m]=='=' || $end_member[0][$m]=='d')continue;
                    if($end_member[0][$m]=='<')break;
                    $p_villages.=$end_member[0][$m]; 
                    //echo $p_villages;
                    //echo $end_member[$k][$m];
                }
                
                //converting strings into int
                
                $player_id[$s][$l][0]=intval($player_t_id);
                //echo $player_id[$s][$l][$k];
                echo $player_t_id;
                echo "<br>";
                $player_t_id="";
                echo $p_name;
                echo "<br>";
                $p_points=str_replace(',','',$p_points);
                $p_points=intval($p_points);
                echo $p_points;
                echo "<br>";
                $p_villages=str_replace(',','',$p_villages);
                $p_villages=intval($p_villages);
                echo $p_villages;
                echo "<br>";
                
                
                
//                 $insert_player = "INSERT INTO player(player_id, t_name, points, villages, wins) VALUES (".$player_id[$s][$l][$k].", '" . mysqli_real_escape_string($con,$p_name) . "' ,$p_points,$p_villages,1)";
//  
//  //$nn = "UPDATE tribal SET t_name = '" . mysqli_real_escape_string($con,$t_name) . "' WHERE world = $s";
//   if ($con->query($insert_player) === TRUE) {
//      echo "New record created successfully";
//   } else {
//      echo "Error: " . $insert_player . "<br>" . $con->error;
//   }
//                            $ww = "UPDATE player SET points = $p_points WHERE points = 0 AND player_id=".$player_id[$s][$l][$k]."";
//  if ($con->query($ww) === TRUE) {
//     echo "New record created successfully";
//  } else {
//echo "Error: " . $ww . "<br>" . $con->error;
//   }            
//$ww = "UPDATE player SET worlds = ".strval($s)." WHERE player_id=".$player_id[$s][$l][$k]."";
//  if ($con->query($ww) === TRUE) {
//     echo "New record created successfully";
//  } else {
//echo "Error: " . $ww . "<br>" . $con->error;
//   }
                

                $p_villages="";
                $p_name="";
                $p_points="";
                
                //SECOUND LAST MEMBER
                for($m=89;$m<=200;$m++){ //getting player id string
                    if($end_member[1][$m]=='i' ||$end_member[1][$m]=='d' ||$end_member[1][$m]=='='  )continue;
                    if($end_member[1][$m]=='"')break;
                    $player_t_id.=$end_member[1][$m];
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+1;$m<=200;$m++){ //getting player name
                    if($end_member[1][$m]=='>' ||$end_member[1][$m]=='"' ||$end_member[1][$m]=='='  )continue;
                    if($end_member[1][$m]=='<')break;
                    $p_name.=$end_member[1][$m]; 
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+strlen($p_name)+16;$m<=300;$m++){ //getting player points
                    if($end_member[1][$m]=='>' ||$end_member[1][$m]=='"' ||$end_member[1][$m]=='='  )continue;
                    if($end_member[1][$m]=='<')break;
                    $p_points.=$end_member[1][$m]; 
                    //echo $p_points;
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+strlen($p_name)+strlen($p_points)+26;$m<=300;$m++){ //getting player count of villages
                    if($end_member[1][$m]=='>' ||$end_member[1][$m]=='"' ||$end_member[1][$m]=='=' || $end_member[1][$m]=='d')continue;
                    if($end_member[1][$m]=='<')break;
                    $p_villages.=$end_member[1][$m]; 
                    //echo $p_villages;
                    //echo $end_member[$k][$m];
                }
                
                // converting string into int
                
                $player_id[$s][$l][1]=intval($player_t_id);
                //echo $player_id[$s][$l][$k];
                echo $player_t_id;
                echo "<br>";
                $player_t_id="";
                echo $p_name;
                echo "<br>";
                $p_points=str_replace(',','',$p_points);
                $p_points=intval($p_points);
                echo $p_points;
                echo "<br>";
                $p_villages=str_replace(',','',$p_villages);
                $p_villages=intval($p_villages);
                echo $p_villages;
                echo "<br>";
                
                
                
//                 $insert_player = "INSERT INTO player(player_id, t_name, points, villages, wins) VALUES (".$player_id[$s][$l][$k].", '" . mysqli_real_escape_string($con,$p_name) . "' ,$p_points,$p_villages,1)";
//  
//  //$nn = "UPDATE tribal SET t_name = '" . mysqli_real_escape_string($con,$t_name) . "' WHERE world = $s";
//   if ($con->query($insert_player) === TRUE) {
//      echo "New record created successfully";
//   } else {
//      echo "Error: " . $insert_player . "<br>" . $con->error;
//   }
//                            $ww = "UPDATE player SET points = $p_points WHERE points = 0 AND player_id=".$player_id[$s][$l][$k]."";
//  if ($con->query($ww) === TRUE) {
//     echo "New record created successfully";
//  } else {
//echo "Error: " . $ww . "<br>" . $con->error;
//                 
//                
//                $ww = "UPDATE player SET worlds = ".strval($s)." WHERE player_id=".$player_id[$s][$l][$k]."";
//  if ($con->query($ww) === TRUE) {
//     echo "New record created successfully";
//  } else {
//echo "Error: " . $ww . "<br>" . $con->error;
    //   }
                $p_villages="";
                $p_name="";
                $p_points="";
                break;
            }
            
            else{
                echo $end_member[$k];
            }
            
            // OTHER PLAYRERS
            
            echo "<br>"; 
            if($k==0){ //first <td>
                
                for($m=73;$m<=120;$m++){
                    if($end_member[$k][$m]=='i' ||$end_member[$k][$m]=='d' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='"')break;
                    $player_t_id.=$end_member[$k][$m];
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+1;$m<=150;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_name.=$end_member[$k][$m]; 
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+strlen($p_name)+16;$m<=300;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_points.=$end_member[$k][$m]; 
                    //echo $p_points;
                    //echo $end_member[$k][$m];
                }
                for($m=75+strlen($player_t_id)+strlen($p_name)+strlen($p_points)+26;$m<=300;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='=' || $end_member[$k][$m]=='d')continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_villages.=$end_member[$k][$m]; 
                    //echo $p_villages;
                    //echo $end_member[$k][$m];
                }
                
            }
            if($k==1){ //secound <td>
                
                 for($m=89;$m<=200;$m++){
                    if($end_member[$k][$m]=='i' ||$end_member[$k][$m]=='d' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='"')break;
                    $player_t_id.=$end_member[$k][$m];
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+1;$m<=200;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_name.=$end_member[$k][$m]; 
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+strlen($p_name)+16;$m<=300;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='='  )continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_points.=$end_member[$k][$m]; 
                    //echo $p_points;
                    //echo $end_member[$k][$m];
                }
                for($m=91+strlen($player_t_id)+strlen($p_name)+strlen($p_points)+26;$m<=300;$m++){
                    if($end_member[$k][$m]=='>' ||$end_member[$k][$m]=='"' ||$end_member[$k][$m]=='=' || $end_member[$k][$m]=='d')continue;
                    if($end_member[$k][$m]=='<')break;
                    $p_villages.=$end_member[$k][$m]; 
                    //echo $p_villages;
                    //echo $end_member[$k][$m];
                }
                
            }
                
            //echo "<br>";
            $player_id[$s][$l][$k]=intval($player_t_id);
            //echo $player_id[$s][$l][$k];
            echo $player_t_id;
            echo "<br>"; 
            echo $p_name;
            echo "<br>"; 
            $p_points=str_replace(',','',$p_points);
            $p_points=intval($p_points);
            echo $p_points;
            echo "<br>"; 
            $p_villages=str_replace(',','',$p_villages);
            $p_villages=intval($p_villages);
            echo $p_villages;
            
            
//            $insert_player = "INSERT INTO player(player_id, t_name, points, villages, wins) VALUES (".$player_id[$s][$l][$k].", '" . mysqli_real_escape_string($con,$p_name) . "' ,$p_points,$p_villages,1)";
//  
//$nn = "UPDATE tribal SET t_name = '" . mysqli_real_escape_string($con,$t_name) . "' WHERE world = $s";
//            $ww = "UPDATE player SET worlds = ".strval($s)." WHERE player_id=".$player_id[$s][$l][$k]."";
//  if ($con->query($ww) === TRUE) {
//     echo "New record created successfully";
//  } else {
//echo "Error: " . $ww . "<br>" . $con->error;
//   }
            
            
            $player_t_id="";
            $p_name="";
            $p_points="";
            $p_villages="";
        }
    }
    
    echo "<hr />";
    $s++; //next world
    
}
?>
