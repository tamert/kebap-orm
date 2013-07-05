<?php
	/*
     * File: Fonksiyonlar.php
     *
	 * Version : 2.0
	 *
     * Description: String Gibi İşlemler İçin Fonksiyonlar Sınıfı.
 	 *
     * Creator: Buğra Güney <e-bugra@bugraguney.com.tr>
     * Created: 16/12/2012
     * 
	 *
	 * Yeni Versiyon İçin Planlananlar :
	 *		
	 *
	 *
	 *
	 */

	class Fonksiyonlar{

        public function __construct(){
            echo "FNK Başladı.<hr>";
        }
        public static function deneme(){
        echo "DEneme içindesin.......";
    }
		public function ResimUpload($klasor,$formelemanadi,$max_en,$max_boy,$orantilasinmi){
				$kaynak = $_FILES[$formelemanadi]["tmp_name"];
				$resimadi = $_FILES[$formelemanadi]["name"];
				$rasgelesayi = rand();
				$sifrelecekresimismi = $resimadi."".$rasgelesayi;
				if($kaynak!=""){
						
				$array = explode('.',$resimadi); 
				$key   = count($array) -1; 
				$sonuc   = $array[$key]; 
			
			$duzeltilecekler = array("JPG","JPEG","GIF","PNG","BMP");
			$degistir = array("jpg","jpg","gif","png","bmp");
			$resimuzanti = str_replace($duzeltilecekler,$degistir,$sonuc);
					$resimadi = substr(uniqid(md5($sifrelecekresimismi)), 0,35).".".$resimuzanti;
					$res = $klasor."/".$resimadi;
						
					if(($resimuzanti=="jpg") or ($resimuzanti=="gif") or ($resimuzanti=="png") or ($resimuzanti=="bmp")){
			
					if(move_uploaded_file($kaynak,$res)){
				ob_start();
				$boyut = getimagesize($res);   
				$en = $boyut[0];
				$boy = $boyut[1];
				$x_oran = $max_en/$en;
				$y_oran = $max_boy/$boy;
				
				if($orantilasinmi=="1"){
				if(($en<=$max_en) and ($boy<=$max_boy)){   
					$son_en  = $en;
					$son_boy = $boy;
				}else if (($x_oran * $boy) < $max_boy){   
					$son_en  = $max_en;
					$son_boy = ceil($x_oran * $boy);
				}else{
					$son_en  = ceil($y_oran * $en);
					$son_boy = $max_boy;
				}
				}else{
					$son_en = $max_en;
					$son_boy = $max_boy;
				}
			
			if($resimuzanti=="jpg"){
				 $eski = imagecreatefromjpeg($res);
				 $yeni = imagecreatetruecolor($son_en,$son_boy);
				 imagecopyresampled($yeni,$eski,0,0,0,0,$son_en,$son_boy,$en,$boy);
				 imagejpeg($yeni,null,-1);
				 $icerik = ob_get_contents();
			}elseif($resimuzanti=="gif"){
				 $yeni=imagecreatetruecolor($son_en,$son_boy);
				 $eski=imagecreatefromgif($res);
				 imagecopyresampled($yeni,$eski,0,0,0,0,$son_en,$son_boy,$en,$boy);
				 imagegif($yeni);
				 $icerik = ob_get_contents();
			}elseif($resimuzanti=="png"){
				 $yeni=imagecreatetruecolor($son_en,$son_boy);
				 $eski=imagecreatefrompng($res);
				 imagecopyresampled($yeni,$eski,0,0,0,0,$son_en,$son_boy,$en,$boy);
				 imagepng($yeni,null,-1);
				 $icerik = ob_get_contents();
			}
			
			
				 ob_end_clean();
				 imagedestroy($eski);
				 imagedestroy($yeni);
			
			$dosya  = fopen($res,"w+");
			fwrite($dosya,$icerik);
			fclose($dosya);
			
					}else{
						$resimadi = 'resim-yok.jpg';
						}
					}else{
						$resimadi = 'resim-yok.jpg';
						}
					}else{
						$resimadi = 'resim-yok.jpg';
						}
						return $resimadi;
	}	
		
		public function TurkceHarfVeKarakteriTemizle($temizlenecekmetin){
			$bul = array("Ç","ç","Ğ","ğ","İ","ı","Ö","ö","Ş","ş","Ü","ü"," ","\"","é","!","'","^","+","%","&","/","(",")","=","?","<",">","£","#","$","½","{","[","]","}","\\","|","@","€","ß",":",".",",",";","*","__");
			$degistir = array("C","c","G","g","I","i","O","o","S","s","U","u","_");
		$string = str_replace($bul,$degistir,$temizlenecekmetin);
		return $string;
	}
		
		public function array_key_exists_multi_search($aranan,$array,$ayrac=","){
			if(is_array($aranan)){
				foreach($aranan as $bul){
					if(array_key_exists($bul,$array)){
						return true;
						}
					}
			}else{
				$bol = explode($ayrac,$aranan);
				foreach($bol as $bul){
					if(array_key_exists($bul,$array)){
						return true;
						}
					}
			}
			return false;
		}
			
		public function ZararliKodTemizle($cumle){
			
			if(is_array($cumle)){
				$guvenliarray = array();
				foreach($cumle as $key=>$value){
						$guvenliarray[$key] = $this->ZararliKodTemizle($value);
				}
					return $guvenliarray;
				}else{
					$cumle = trim($cumle);
					if( is_null($cumle) ) return 'NULL';
					if( is_numeric($cumle) ) return $cumle;
			
					if( get_magic_quotes_gpc() ) {
						$cumle = stripslashes($cumle);
					}
					$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
					$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
					return str_replace($search, $replace, $cumle);
					}
			
		
//			$cumle = mysqli_escape_string($cumle);
//			return $cumle;
		}
			
	}
?>