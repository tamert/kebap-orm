<?php
	/*
     * File: MysqlClass.php
     *
	 * Version : 2.2
	 *
     * Description: Mysql Server İçin Veritabanı Sınıfı.
 	 *
     * Creator: Buğra Güney <e-bugra@bugraguney.com.tr>
     * Created: 16/12/2012
     * 
	 *
	 * Yeni Versiyon İçin Planlananlar :
	 *		Where koşulu geliştirilmesi... Hata varrsa son sorgu gösterilmeli...
	 *		is_array olmayan stringler için döngü yazılacak
	 *
	 *
	 */

class MysqlVeriTabani{
	
	private static $MysqlServer = "bugraguney.com.tr";
	private static $MysqlKullaniciAdi = "bugragun_ar";
	private static $MysqlKullaniciSifresi = "ASD123qwe";
	private static $MysqlDbAdi = "bugragun_ar";
	
	private $Baglanti;
	public $EnSonSorgununSuresi;
	public $ToplamSorguSayisi;
	public $ToplamSorguSuresi;
	public $SonucSayisi;
	public $SonKullanilanid;
	public $SonSorguCumlesi;
	private $infbilgileri;
	private $infsonuc;
	private $hata;
	private $sorgudurdur;
	
	public function __construct(){
		$this->Baglanti = null;
		$this->hata = array();
		$this->EnSonSorgununSuresi = 0;
		$this->ToplamSorguSayisi = 0;
		$this->sorgudurdur = 0;
		$this->ToplamSorguSuresi = 0;
		$this->SonucSayisi = 0;
		$this->SonKullanilanid = 0;
		$this->SonSorguCumlesi = "";
		$this->Baglan();
		$this->infcontrol();
	}

	private function infcontrol(){
		$infsor = mysql_query("select TABLE_NAME,COLUMN_NAME,COLUMN_TYPE from information_schema.COLUMNS where TABLE_SCHEMA='".self::$MysqlDbAdi."'");
				if($infsor){
						$this->infsonuc = mysql_num_rows($infsor);
						while($infyaz=mysql_fetch_object($infsor)){
							$this->infbilgileri[$infyaz->TABLE_NAME][$infyaz->COLUMN_NAME] = $infyaz->COLUMN_TYPE;
							}
						$this->infbilgileri = (object)$this->infbilgileri;
				}else{
					$this->infsonuc = 0;
					
					$this->infbilgileri = null;
				}
	}
	
	private function HataKaydet($tip,$baslik,$hatametni){
		$this->hata[] = array($tip,$baslik,$hatametni);
		}
	
	public function __destruct(){
		$this->BaglantiKapat();
	}
	
	
	private function Where($kosul,$table){
						
		if($kosul==""){
			$this->HataKaydet('uyari',"Where Koşulu Boş","Koşul Gönderilmedi.");
				return "";
		}
		
		$exportwhere = " where ";
		$x=0;
		if($this->infsonuc==0){
				if(is_array($kosul)){
					foreach($kosul as $column=>$value){
						($x==0)?'':$exportwhere.=' and ';
						if($value=="NOW()"){
							$exportwhere .= $column."='".$value."'";
						}else{
							$exportwhere .= $column."=".$value;
						}
						$x++;
					}
				}else{

					$bul = explode("=",$kosul);
					$bul2 = explode("like",$kosul);
					if(count($bul)>1 or count($bul2)>1){
						$exportwhere .= $kosul;
					}else{
						$exportwhere .= "id='".$kosul."'";
					}
				}
		}else{
			if(!isset($this->infbilgileri->$table)){
							$this->HataKaydet("hata","Tablo Bulunamadı.","<b>".$table."</b> tablosu information da bulunamadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
							return false;
						}
						
				if(is_array($kosul)){
					foreach($kosul as $column=>$value){
						if(array_key_exists($column,$this->infbilgileri->$table)){
							($x==0)?'':$exportwhere.=' and ';
							if($value=="NOW()"){
								$exportwhere .= $column."=".$value;
							}else{
								$exportwhere .= $column."='".$value."'";
							}
							$x++;
						}else{
							$this->HataKaydet('uyari',"Where Komutu Düzenlendi","İnformation Tablosunda <b>".$column."</b> Alanı Bulunamadı.Where Koşulundan Kaldırıldı.");
							}
					}
					if($x==0){
						$exportwhere = "";
						$this->HataKaydet('hata',"Where Komutu Silindi","Where Koşulunda Veri Kalmadı.");
						$this->sorgudurdur = 1;
						$this->HataGoster();
						}
				}else{
					
					
					$bul = explode("=",$kosul);
					$bul2 = explode("like",$kosul);
					if(count($bul)>1 or count($bul2)>1){
						$exportwhere .= $kosul;
		/*				for($y=0;$y<count($bul);$y++){
							if(array_key_exists($bul[$y],$this->infbilgileri->$table)){
								($x==0)?'':$exportwhere.=' and ';
									$exportwhere .= $bul[$y]."='".$bul[$y+1]."'";
								$x++;
							}else{
								$this->HataKaydet('uyari',"Where Komutu Düzenlendi","İnformation Tablosunda <b>".$bul[$y]."</b> Alanı Bulunamadı.Where Koşulundan Kaldırıldı.");
								}
								$y++;
						}
						
						
						
							
						if($x==0){
							$exportwhere = "";
							$this->HataKaydet('hata',"Where Komutu Silindi","Where Koşulunda Veri Kalmadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
						}	*/
						
					}else{
						$exportwhere .= "id='".$kosul."'";
					}
				}
			}
		return $exportwhere;
	}
	
	public function Update($table,$veri,$kosul){
		if( ! $this->Baglanti ){
			return false;
		}
		if($this->sorgudurdur==1){
			return false;
		}
			if($veri==""){
				$this->HataKaydet("hata","Veri Bulunamadı","Veri İçeriği Boş Olamaz.");
				$this->sorgudurdur = 1;
				$this->HataGoster();
				return false;
			}
			if($this->infsonuc==0){
				$sorgucumlesi = "update ".$table." set ";
				if(is_array($veri)){
					$dongusayisi = 0;
					foreach($veri as $ColumnName=>$Value){
						($dongusayisi==0)?'':$sorgucumlesi.=",";
						if($Value=="NOW()"){
							$sorgucumlesi.=$ColumnName."=".$Value;	
							}else{
								$sorgucumlesi.=$ColumnName."='".$Value."'";							
								}
					
						$dongusayisi=1;
					}
				}else{
					$sorgucumlesi.=$veri;
					}
					$sorgucumlesi.=$this->Where($kosul,$table);
					if($this->sorgudurdur==1){
						return false;
						}
				return $this->Sorgu($sorgucumlesi);
			}else{
				
				$sorgucumlesi = "update ".$table." set ";
				$dongusayisi = 0;
				
				if(!isset($this->infbilgileri->$table)){
							$this->HataKaydet("hata","Tablo Bulunamadı.","<b>".$table."</b> tablosu information da bulunamadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
							return false;
						}
						if(is_array($veri)){
							foreach($veri as $ColumnName=>$Value){
								if(array_key_exists($ColumnName,$this->infbilgileri->$table)){
									($dongusayisi==0)?'':$sorgucumlesi.=",";
									if($Value=="NOW()"){
										$sorgucumlesi.=$ColumnName."=".$Value;
									}else{
										$sorgucumlesi.=$ColumnName."='".$Value."'";
									}
									$dongusayisi=1;
									}else{
										$this->HataKaydet('uyari',"Update Komutu Düzenlendi","İnformation Tablosunda <b>".$ColumnName."</b> Alanı Bulunamadı.Update Sorgusundan Kaldırıldı.");
										}
							}	
							
							if($dongusayisi==0){
							$sorgucumlesi = "";
								$this->HataKaydet('hata',"Update Komutu Silindi.","Güncellenecek Alanlar Tabloda Bulunamadı.");
								$this->sorgudurdur = 1;
								$this->HataGoster();
							}
							
						}else{
							$sorgucumlesi.= $veri;
							}
					
					$sorgucumlesi.= $this->Where($kosul,$table);
					
					
					if($this->sorgudurdur==1){
						return false;
						}
						return $this->Sorgu($sorgucumlesi);
						
				}		
	}
	
	public function Delete($table,$kosul){
		if( ! $this->Baglanti ){
			return false;
		}
		if($this->sorgudurdur==1){
			return false;
		}
		if($this->infsonuc==1){
			if(!isset($this->infbilgileri->$table)){
							$this->HataKaydet("hata","Tablo Bulunamadı.","<b>".$table."</b> tablosu information da bulunamadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
							return false;
						}
			}
			$silsql = "delete from ".$table.$this->Where($kosul,$table);
				if($this->sorgudurdur==1){
					return false;
					}
				return $this->Sorgu($silsql);
	}
	
	public function Insert($table,$veri){
		if( ! $this->Baglanti ){
			return false;
		}
		if($this->sorgudurdur==1){
			return false;
		}
		if($veri==""){
			$this->HataKaydet("hata","Veri Bulunamadı","Veri İçeriği Boş Olamaz.");
			$this->sorgudurdur = 1;
			$this->HataGoster();
			return false;
			}
		
			if($this->infsonuc==0){
				$sorgucumlesi = "insert into ".$table." (";
					if(is_array($veri)){
						$dongusayisi = 0;
						foreach($veri as $ColumnName=>$Value){
							($dongusayisi==0)?'':$sorgucumlesi.=",";
							$sorgucumlesi.=$ColumnName;
							$dongusayisi=1;
						}
						$sorgucumlesi.=") values (";
						$dongusayisi = 0;
						foreach($veri as $ColumnName=>$Value){
							($dongusayisi==0)?'':$sorgucumlesi.=",";
							if($Value=="NOW()"){
								$sorgucumlesi.=$Value;
							}else{
								$sorgucumlesi.="'".$Value."'";
							}
							$dongusayisi=1;
						}
						$sorgucumlesi.=")";
						
					}else{
						$sorgucumlesi.=$veri;
						}
				return $this->Sorgu($sorgucumlesi);
			}else{
						$sorgucumlesi = "insert into ".$table." (";
						$dongusayisi = 0;
						if(!isset($this->infbilgileri->$table)){
							$this->HataKaydet("hata","Tablo Bulunamadı.","<b>".$table."</b> tablosu information da bulunamadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
							return false;
						}
							if(is_array($veri)){
								foreach($veri as $ColumnName=>$Value){
									if(array_key_exists($ColumnName,$this->infbilgileri->$table)){
										($dongusayisi==0)?'':$sorgucumlesi.=",";
										$sorgucumlesi.=$ColumnName;
										$dongusayisi=1;	
										}else{
											$this->HataKaydet('uyari',"İnsert Komutu Düzenlendi","İnformation Tablosunda <b>".$ColumnName."</b> Alanı Bulunamadı.İnsert Sorgusundan Kaldırıldı.");
											}
								}
								
								if($dongusayisi==0){
								$sorgucumlesi = "";
									$this->HataKaydet('hata',"İnsert Komutu Silindi.","Kayıt Girilecek Alanlar Tabloda Bulunamadı.");
									$this->sorgudurdur = 1;
									$this->HataGoster();
									return false;
								}
								
								$sorgucumlesi.=") values (";
								$dongusayisi = 0;
								foreach($veri as $ColumnName=>$Value){
									if(array_key_exists($ColumnName,$this->infbilgileri->$table)){
										($dongusayisi==0)?'':$sorgucumlesi.=",";
										if($Value=="NOW()"){
											$sorgucumlesi.=$Value;
										}else{
											$sorgucumlesi.="'".$Value."'";
										}
										$dongusayisi=1;	
										}
								}
								$sorgucumlesi.=")";
								
								
							}else{
								$sorgucumlesi.=$veri;
								}
							if($this->sorgudurdur==1){
								return false;
								}
								
							return $this->Sorgu($sorgucumlesi);
						
				}
	}

	private function OrderBy($order){
		if($order==""){
			return "";
			}
		if(is_array($order)){
			$ordercumlesi = " ORDER BY ";
			$x = 0;
			foreach($order as $key=>$value){
				if($x==0){
					
					}else{
						$ordercumlesi.= ", ";
						}
				$ordercumlesi.= $key." ".$value;
				$x++;
				}
				return $ordercumlesi;
			}else{
				return " ORDER BY ".$order;
				}
		}

	private function Limit($limit){
		if($limit==""){
			return "";
			}
		if(is_array($limit)){
			$limitcumlesi = " LIMIT ";
				for($x=1;$x<=count($limit);$x++){
					if($x==2){
							$limitcumlesi.= ",";
						}
					$y = $x-1;
					$limitcumlesi.= $limit[$y];
					}
				return $limitcumlesi;
			}else{
				return " LIMIT ".$limit;
				}
		}

	public function Select($table,$alanlar="*",$kosul="",$order="",$limit="",$obj=""){
		if( ! $this->Baglanti ){
			return false;
		}
		if($this->sorgudurdur==1){
			return false;
		}
		if($alanlar==""){
			$alanlar = "*";
			}
		

		
			if($this->infsonuc==0){
				$sorgucumlesi = "select ";
							if(is_array($alanlar)){
								$secilecekalanlar = "";
								$dongusayisi = 0;
								foreach($alanlar as $alan){
									($dongusayisi==0)?'':$secilecekalanlar.=",";
									$secilecekalanlar.=$alan;
									$dongusayisi=1;
									}
									if($dongusayisi==0){
										$secilecekalanlar = "*";
										}
								}else{
									$secilecekalanlar = $alanlar;
									}
					$sorgucumlesi.=$secilecekalanlar." from ".$table;
					$sorgucumlesi.=$this->Where($kosul,$table);
					$sorgucumlesi.=$this->OrderBy($order);
					$sorgucumlesi.=$this->Limit($limit);
					if($this->sorgudurdur==1){
						return false;
					}
				return $this->Sorgu($sorgucumlesi,$obj);
			}else{
				if(!isset($this->infbilgileri->$table)){
							$this->HataKaydet("hata","Tablo Bulunamadı.","<b>".$table."</b> tablosu information da bulunamadı.");
							$this->sorgudurdur = 1;
							$this->HataGoster();
							return false;
						}
						
				$sorgucumlesi = "select ";	
						
							if(is_array($alanlar)){
								$secilecekalanlar = "";
								$dongusayisi = 0;
								foreach($alanlar as $alan){
									if(array_key_exists($ColumnName,$this->infbilgileri->$table)){
										($dongusayisi==0)?'':$secilecekalanlar.=",";
										$secilecekalanlar.=$alan;
										$dongusayisi=1;
									}else{
										$this->HataKaydet('uyari',"Select Komutu Düzenlendi","İnformation Tablosunda <b>".$ColumnName."</b> Alanı Bulunamadı.Select Sorgusundan Kaldırıldı.");
										}
								}
									if($dongusayisi==0){
										$secilecekalanlar = "*";
										$this->HataKaydet('uyari',"Select Komutu Düzenlendi","İnformation Tablosunda <b>".$ColumnName."</b> Alanı Bulunamadı.Select Sorgusu * Şeklinde Düzenlendi.");
										}
								}else{
									$secilecekalanlar = $alanlar;
									}
					$sorgucumlesi.=$secilecekalanlar." from ".$table;
					$sorgucumlesi.=$this->Where($kosul,$table);
					$sorgucumlesi.=$this->OrderBy($order);
					$sorgucumlesi.=$this->Limit($limit);
					if($this->sorgudurdur==1){
						return false;
					}
				return $this->Sorgu($sorgucumlesi,$obj);
						
			}
	}
	

	public function Sorgu($sorgu,$obj=""){
		$this->SonSorguCumlesi = $sorgu;
	
		if( ! $this->Baglanti ){
			return false;
		}
		
		
		$this->SonucSayisi = 0;
		$this->SonKullanilanid = 0;
		
		if($this->sorgudurdur==1){
			$this->HataKaydet('hata',"Sorgu Durdurma Aktif.","Çalıştırılamayan Sorgu : ".$sorgu);
			$this->HataGoster();
			return false;
		}
		
		$baslangiczamani = $this->AnlikZaman();
		$sorgusonucu = mysql_query($sorgu);
		$bitiszamani = $this->AnlikZaman();
		$this->ToplamSorguSayisi++;
		$this->EnSonSorgununSuresi = number_format(($bitiszamani - $baslangiczamani),5);
		$this->ToplamSorguSuresi += $this->EnSonSorgununSuresi;
		if($sorgusonucu){
		if( $sorgusonucu === true ) {
            $this->SonKullanilanid = mysql_insert_id($this->Baglanti);
            return true;
        }
		
		$sonuc = array();
        while( $satir = mysql_fetch_object($sorgusonucu) ) {
            $sonuc[] = $satir;
        }
        mysql_free_result($sorgusonucu);
        $this->SonucSayisi = count($sonuc);
			if($obj==1 and $this->SonucSayisi>0){
				return $sonuc[0];
			}else{
		        return $sonuc;
			}
		}else{
			$this->HataKaydet("hata","Sorgu Hatası","Sorgu : ".$sorgu);
			$this->sorgudurdur = 1;
			$this->HataGoster();
			return false;
			}
		
	}
	
	public static function SetDbBilgileri($MysqlServer,$MysqlKullaniciAdi,$MysqlKullaniciSifresi,$MysqlDbAdi){
			self::$MysqlServer = $MysqlServer;
			self::$MysqlKullaniciAdi = $MysqlKullaniciAdi;
			self::$MysqlKullaniciSifresi = $MysqlKullaniciSifresi;
			self::$MysqlDbAdi = $MysqlDbAdi;
	}
	
		public function HataGoster(){
			$this->HataKaydet('bilgi',"Son Sorgu",$this->SonSorguCumlesi);
			echo '<table border="0" cellpadding="2" cellspacing="2" width="100%">';
			foreach($this->hata as $hata){
				if($hata[0]=="hata"){
					$arkaplanrengi = "#F79646";
					$yazirengi = "white";
					}elseif($hata[0]=="uyari"){
						$arkaplanrengi = "#538DD5";
						$yazirengi = "white";
						}elseif($hata[0]=="bilgi"){
							$arkaplanrengi = "#9BBB59";
							$yazirengi = "white";
							}
				
					echo "<tr><td style=\"background-color:".$arkaplanrengi.";color:".$yazirengi."\">".$hata[0]."</td><td style=\"background-color:".$arkaplanrengi.";color:".$yazirengi."\">".$hata[1]."</td><td style=\"background-color:".$arkaplanrengi.";color:".$yazirengi."\">".$hata[2]."</td></tr>";
				}
			echo "</table>";
	
		}
	
	private function Baglan(){
		$this->Baglanti = @mysql_connect(self::$MysqlServer,self::$MysqlKullaniciAdi,self::$MysqlKullaniciSifresi);
		if (! $this->Baglanti) die ("Mysql Server'a Baglanilamadi...");
		mysql_select_db(self::$MysqlDbAdi,$this->Baglanti)	or die("Veritabanina Baglanilamadi.");
		mysql_query("SET NAMES 'utf8'");
		mysql_query("SET CHARACTER SET utf8");
		mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");
	}
	
	private function BaglantiKapat(){
		if( $this->Baglanti ) {
            mysql_close($this->Baglanti);
            $this->Baglanti = null;
		    return;
        }
	}
	
	private function AnlikZaman(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	
}


/* 
	include("class/MysqlClass.php");
	MysqlVeriTabani::SetDbBilgileri("localhost","root","root","vt");
	$vt = new MysqlVeriTabani();

*/

?>