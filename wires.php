<?php

$data= date("d/m/Y");

// $file_patch_1 = 'C:\xampp\htdocs\komax_565'.date("Y").'\\'.date("m").'\\'.date("d").'\Producti.SDC';
$file_patch_1 = 'C:\xampp\htdocs\komax_565'.'\\'.date("m").'\\'.date("d").'\Producti.SDC';

$plik = file($file_patch_1);

$zakres_tablicy = count($plik);
$tablica_danych_przewody= array();
$ostatnia_pozycja_tablicy_przew = 0;
for ($i=0; $i<$zakres_tablicy; $i++){
    //zestawienie przewodow
    if(strstr($plik[$i], "ArticleKey")){
        $artykul = ltrim(substr($plik[$i], 11, strlen($plik[$i])), " \n\r\t\v\x00");
        //echo $artykul;
    }
    if(strstr($plik[$i], "Job")){
        $job = ltrim(substr($plik[$i], 4, strlen($plik[$i])), " \n\r\t\v\x00");
        $job2 = explode(",", $job);  //odcinam lead set
        $job = $job2[0];
        //echo $job;
    }
    if(strstr($plik[$i], "[Counter]")&&strstr($plik[$i+2], "Wire")){

        //data operacji
        $wiersz_rozbicie2 = explode(",", $plik[$i+1]);
        $data_operacji = substr($wiersz_rozbicie2[0], 14, 21);
        //kod przewodu
        $wiersz_rozbicie = explode(",", $plik[$i+2]);
        $kod_przewodu = substr($wiersz_rozbicie[0], 5, 15);
        //licznik przewodu
        if(isset($wiersz_rozbicie[1])){
            $licznik_przewodu = $wiersz_rozbicie[1];
        }
        

        $tablica_danych_przewody[$ostatnia_pozycja_tablicy_przew][1]=$data_operacji."||".$job."||".$artykul."||".$kod_przewodu;
        $tablica_danych_przewody[$ostatnia_pozycja_tablicy_przew][2]=$licznik_przewodu;
        $ostatnia_pozycja_tablicy_przew++;
    }
}

//sumuję zużycie przewodu
$tablica_zestawienie=array();
$dodaj_do_zestawienia=1;
for($i=0; $i<count($tablica_danych_przewody); $i++){
    if(count($tablica_zestawienie)==0){
        $tablica_zestawienie[0][1]= $tablica_danych_przewody[0][1];
        $tablica_zestawienie[0][2]= $tablica_danych_przewody[0][2];
        $tablica_zestawienie[0][3]= 0;
    }
    $dodaj_do_zestawienia=1;
    for($j=0; $j<count($tablica_zestawienie); $j++){
        if($tablica_zestawienie[$j][1]== $tablica_danych_przewody[$i][1]){
            //tutaj obliczam ilosc
            $tablica_zestawienie[$j][2]=$tablica_danych_przewody[$i][2]-$tablica_zestawienie[$j][3];
            $dodaj_do_zestawienia=0;
        }
    }
    if($dodaj_do_zestawienia==1){
        $tymczasowa = count($tablica_zestawienie);
        $tablica_zestawienie[$tymczasowa][1]=$tablica_danych_przewody[$i][1];
        $tablica_zestawienie[$tymczasowa][2]=0;  //tu bedzie wynik
        $tablica_zestawienie[$tymczasowa][3]=$tablica_danych_przewody[$i][2];  //przechowuje pierwszy zapis z listy
    }

}
echo "Zestawienie przewodów komax 565 na dzień: ".$data."<br><br>";

echo '<style> 
.sekcja_0 {
    display: flex;
    justify-content: center;
}
td { 
    border: 1px solid black; 
    width: 150px;
} 
.kol1{
    width:220px;
}
.kol2{
    width:90px;
}
</style>';

echo '<div class="sekcja_0">';
    echo '<table>';
    for($i=0; $i<count($tablica_zestawienie); $i++){
        $wiersz_zestawienie = explode("||", $tablica_zestawienie[$i][1]);
        if($wiersz_zestawienie[0]==$data){
            echo '<tr>';
            echo '
            <td class="kol1">'.$wiersz_zestawienie[1].'</td>
            <td class="kol1">'.$wiersz_zestawienie[2].'</td>
            <td class="kol2">'.$wiersz_zestawienie[3].'</td>
            <td class="kol2">'.$tablica_zestawienie[$i][2]*0.001.'m</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
echo '</div>';

unset($tablica_zestawienie);

?>
