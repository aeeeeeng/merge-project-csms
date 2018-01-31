<?
session_start();
include ('../include/ex_fungsi.php');
include ('../include/validasi.php');
$fungsi=new ex_fungsi();
$conn=$fungsi->ex_koneksi();

$halaman_id=170;
$user_id=$_SESSION['user_id'];
$user_org=$_SESSION['user_org'];
$show_ket='';

if ($fungsi->keamanan($halaman_id,$user_id)==0) {
?>
				<SCRIPT LANGUAGE="JavaScript">

					alert("Anda tidak berhak meng akses halaman ini.... \n Login Dahulu...");

				</SCRIPT>
	 <a href="../index.php">Login....</a>
<?
exit();
}


$page="create_invoice_bag_darat.php";

$vendor=$fungsi->ex_find_vendor($conn,$user_id);

//$vendor='0000410019';
$hanya_baca = $fungsi->ex_hanya_baca($vendor);

$no_shipment = $_POST['no_shipment'];
$distributor = $_POST['distributor'];
$tipe_transaksi = $_POST['tipe_transaksi'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$warna_plat = $_POST['warna_plat'];
$tahun = date("Y");
if (isset($_POST['tahun']))
$tahun=$_POST['tahun'];
$bulan = $_POST['bulan'];
$plant = $_POST['plant'];
$tgl_tremin=$bulan.$tahun;
$termin = $_POST['termin'];

$currentPage="create_invoice_bag_darat.php";
$komen="";
if(isset($_POST['cari'])){
        $tahun=$_POST['tahun'];
  	$bulan = $_POST['bulan'];
	$tgl_tremin=$bulan.$tahun;
        $termin = $_POST['termin'];

        $mp_coics=$fungsi->getComin($conn,$user_org);
        if(count($mp_coics)>0){
            unset($inorg);$orgcounter=0;
            foreach ($mp_coics as $keyOrg => $valorgm){
                  $inorg .="'".$keyOrg."',";
                  $orgcounter++;
            }
            $orgIn= rtrim($inorg, ',');
        }else{
           $orgIn= $user_org;
        }

	$jumHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        //echo "Tahun : ".$id_tahun." : ".$jumHari;

	if ($termin==1)
       {
	    $tanggal_mulai_sql='1-'.$bulan.'-'.$tahun;
	    $tanggal_selesai_sql='10-'.$bulan.'-'.$tahun;
		}
	ELSEif ($termin==2)
	   {
	    $tanggal_mulai_sql='11-'.$bulan.'-'.$tahun;
	   	$tanggal_selesai_sql='20-'.$bulan.'-'.$tahun;
		}
	ELSEif ($termin==3)
	   {
		$tanggal_mulai_sql='21-'.$bulan.'-'.$tahun;
		$tanggal_selesai_sql=$jumHari.'-'.$bulan.'-'.$tahun;
		}
	ELSEif ($termin==4)
	   {
		$tanggal_mulai_sql='01-'.$bulan.'-'.$tahun;
		$tanggal_selesai_sql=$jumHari.'-'.$bulan.'-'.$tahun;
		}
	ELSE {

        $tanggal_mulai_sql='01-'.$bulan.'-'.$tahun;
		$tanggal_selesai_sql=$jumHari.'-'.$bulan.'-'.$tahun;
    }
	if($plant=="" and $no_shipment=="" and $distributor=="" and $vendor=="" and $tipe_transaksi == "" and $tahun == "" and $bulan == "" and $warna_plat == ""){
		$sql= "SELECT * FROM EX_PAJAK_HDR_V WHERE  TANGGAL_KIRIM BETWEEN TO_Date('$tanggal_mulai_sql', 'DD-MM-YYYY') AND TO_Date('$tanggal_selesai_sql', 'DD-MM-YYYY') AND DELETE_MARK = '0' AND ORG in ($orgIn) AND STATUS = 'OPEN' AND STATUS2 = 'OPEN' AND VEHICLE_TYPE<>'205' AND KELOMPOK_TRANSAKSI = 'DARAT' AND TIPE_TRANSAKSI = 'BAG' AND STATUS_PAJAK = 'OK' ORDER BY ORG,NAMA_VENDOR,NO_SHP_TRN ASC";
	}else {
		$pakeor=0;
		$sql= "SELECT *FROM EX_PAJAK_HDR_V WHERE ";
		if($no_shipment!=""){
		$sql.=" NO_SHP_TRN LIKE '$no_shipment' ";
		$pakeor=1;
		}
		if($distributor!=""){
			if($pakeor==1){
			$sql.=" AND ( NAMA_SOLD_TO LIKE '$distributor' OR SOLD_TO LIKE '$distributor' ) ";
			}else{
			$sql.=" ( NAMA_SOLD_TO LIKE '$distributor' OR SOLD_TO LIKE '$distributor' ) ";
			$pakeor=1;
			}
		}
		if($no_shipment!=""){
			if($pakeor==1){
			$sql.=" AND NO_SHP_TRN LIKE '$no_shipment' ";
			}else{
			$sql.=" NO_SHP_TRN LIKE '$no_shipment' ";
			$pakeor=1;
			}
		}
		if($vendor!=""){
			if($pakeor==1){
			$sql.=" AND ( NAMA_VENDOR LIKE '$vendor' OR VENDOR LIKE '$vendor' ) ";
			}else{
			$sql.=" ( NAMA_VENDOR LIKE '$vendor' OR VENDOR LIKE '$vendor' ) ";
			$pakeor=1;
			}
		}
		if($plant!=""){
			if($pakeor==1){
			$sql.=" AND PLANT LIKE '$plant' ";
			}else{
			$sql.=" PLANT LIKE '$plant' ";
			$pakeor=1;
			}
		}
		if($tipe_transaksi!=""){
			if($pakeor==1){
			$sql.=" AND TIPE_TRANSAKSI LIKE '$tipe_transaksi' ";
			}else{
			$sql.=" TIPE_TRANSAKSI LIKE '$tipe_transaksi' ";
			$pakeor=1;
			}
		}
		if($bulan!="" or $tahun!=""){

//			if ($tanggal_mulai=="")$tanggal_mulai_sql = "01-01-1990";
//			else $tanggal_mulai_sql = $tanggal_mulai;
//			if ($tanggal_selesai=="")$tanggal_selesai_sql = "12-12-9999";
//			else $tanggal_selesai_sql = $tanggal_selesai;
//			if($pakeor==1){
//			$sql.=" AND TANGGAL_KIRIM BETWEEN TO_Date('$tanggal_mulai_sql', 'DD-MM-YYYY') AND TO_Date('$tanggal_selesai_sql', 'DD-MM-YYYY') ";
//			}else{
//			$sql.=" TANGGAL_KIRIM BETWEEN TO_Date('$tanggal_mulai_sql', 'DD-MM-YYYY') AND TO_Date('$tanggal_selesai_sql', 'DD-MM-YYYY') ";
//			$pakeor=1;
//			}
			if ($tahun=="")$tahun = date("Y");
			if ($bulan=="")$bulan = date("m");

			if($pakeor==1){
			$sql.=" AND TANGGAL_KIRIM BETWEEN TO_Date('$tanggal_mulai_sql', 'DD-MM-YYYY') AND TO_Date('$tanggal_selesai_sql', 'DD-MM-YYYY')";
			}else{
			$sql.="  TANGGAL_KIRIM BETWEEN TO_Date('$tanggal_mulai_sql', 'DD-MM-YYYY') AND TO_Date('$tanggal_selesai_sql', 'DD-MM-YYYY') ";
			$pakeor=1;
			}

		}
		if($warna_plat!=""){
			if($pakeor==1){
			$sql.=" AND WARNA_PLAT LIKE '$warna_plat' ";
			}else{
			$sql.=" WARNA_PLAT LIKE '$warna_plat' ";
			$pakeor=1;
			}
		}
		$sql.=" AND DELETE_MARK = '0' AND ORG in ($orgIn) AND STATUS = 'OPEN' AND STATUS2 = 'OPEN' AND VEHICLE_TYPE<>'205' AND KELOMPOK_TRANSAKSI = 'DARAT' AND TIPE_TRANSAKSI = 'BAG' AND STATUS_PAJAK = 'OK' ORDER BY ORG,VENDOR, SAL_DISTRIK, NO_SHP_TRN ASC";
	}

//	echo $sql;
	$query= oci_parse($conn, $sql);
	oci_execute($query);

	while($row=oci_fetch_array($query)){
                $com[]=$row[ORG];
		$no_shipment_v[]=$row[NO_SHP_TRN];
		$tgl_kirim_v[]=$row[TANGGAL_KIRIM];
		$produk_v[]=$row[NAMA_PRODUK];
		$plant_v[]=$row[PLANT];
		$no_pol_v[]=$row[NO_POL];
		$sal_distrik_v[]=$row[SAL_DISTRIK];
		$sold_to_v[]=$row[SOLD_TO];
		$nama_sold_to_v[]=$row[NAMA_SOLD_TO];
		$qty_v[]=$row[QTY_SHP];
		$qty_kantong_rusak_v[]=$row[QTY_KTG_RUSAK];
		$qty_semen_rusak_v[]=$row[QTY_SEMEN_RUSAK];
		$id_v[]=$row[ID];
		$tarif_cost_v[]=$row[TARIF_COST];
		$shp_cost_v[]=$row[SHP_COST];
		$no_vendor_v=$row[VENDOR];
		$nama_vendor_v=$row[NAMA_VENDOR];
		$spt_cek=$row[SPT_PAJAK];

	#ECHO'<BR>'.	$sqlok="select * from EX_INVOICE WHERE NO_VENDOR='".$row[VENDOR]."'";
				$sqlok="select tgl_termin, termin from EX_INVOICE WHERE NO_VENDOR='".$row[VENDOR]."' and tgl_termin='".$tgl_tremin."' and termin is not null group by tgl_termin, termin";
				$queryok= oci_parse($conn, $sqlok);
				@oci_execute($queryok); $arr_termin = array();
				while($data=@oci_fetch_array($queryok)){
					$novendor[]=$data['NO_VENDOR'];
			   	    $tgl_tremin1=$data['TGL_TERMIN'];
					$termin1=$data['TERMIN'];
					$arr_termin[$termin1] = $tgl_tremin1;
				}
				//echo "<br><br>-----dor";

				//print_r($arr_termin);
	}

	$lanjut = false;
	if (@array_key_exists($termin, $arr_termin)) {
		$lanjut = true;
	}

	if ($lanjut)
	{
	  echo "<script>alert('Tremin Sudah ada');</script>";
	  exit();
	}
	$total=count($no_shipment_v);
	if ($total < 1)$komen = "Tidak Ada Data Yang Ditemukan";

}else if(isset($_POST['action'])){

        $no_pajak_vendor_cek=trim($_POST['pjk2']).trim($_POST['pjk3']).trim($_POST['pjk4']);
        $tanggal_pjkceke=trim($_POST['tanggal_pjk']);
        $kepalapjak=trim($_POST['pjk1']);
        $cektahun=trim($_POST['tahun']);

        if($no_pajak_vendor_cek!='' && $tanggal_pjkceke!=''){
            //$no_pajak_vendor_cek."<br>";

            //parameter tanggal
            $pecah2 = explode("-", $_POST['tanggal_pjk']);
            $date2 = $pecah2[0];
            $month2 = $pecah2[1];
            $year2 = $pecah2[2];
            $fromat2=$year2.$month2.$date2;

            if($fromat2>='20130401'){
                     //Koneksi SAP
                     //$link_koneksi_sap = "/opt/lampp/htdocs/sgg/include/connect/sap_sd_210.php";
                     $sap = new SAPConnection();
                     $sap->Connect("../include/sapclasses/logon_data.conf");
                     //$sap->Connect($link_koneksi_sap);
                     if ($sap->GetStatus() == SAPRFC_OK ) $sap->Open ();
                        if ($sap->GetStatus() != SAPRFC_OK ) {
                        echo $sap->PrintStatus();
                        exit;
                     }
                    $fce = &$sap->NewFunction ("ZAPPSD_DISPLAYFAKTUR");
                    if ($fce == false ) {
                       $sap->PrintStatus();
                       exit;
                    }
                    $org_fpajakac=trim($_POST['org_fpajak']);
                    if($org_fpajakac!=''){
                        $orgIn4st = $org_fpajakac;
                    }else{
                        $orgIn4st = $user_org;
                    }

                    //$fce->I_BUKRS=$orgIn4st;//$user_org;
                    $fce->I_LIFNR=$vendor;
                    $fce->I_BUDAT=$fromat2;
                    $fce->I_XBLNR=$kepalapjak.$no_pajak_vendor_cek;

                    $fce->Call();
                    if ($fce->GetStatus() == SAPRFC_OK ) {
                    $fce->T_DATA->Reset();
                    $q = 0;
                    unset($arrayu);
                    $arrayu=array();
                    $FPNUMH='';$FPNUML='';
                    while ( $fce->T_DATA->Next() ){
                            //filter data
                            $arrayu[]=$fce->T_DATA->row;
                            $FPNUML=$fce->T_DATA->row['FPNUML'];
                            $FPNUMH=$fce->T_DATA->row['FPNUMH'];
                    }


                    $tipeerr=$fce->RETURN['TYPE'];
                    $pesansap=$fce->RETURN['MESSAGE'];

                    }else{
                            $fce->PrintStatus();
                            $fce->Close();
                            $sap->Close();
                    }

                    if($tipeerr=='S'){
                       // echo $show_ket =$FPNUML." | ".$FPNUMH." Ketemu";

//                               echo "<pre>";
//                               print_r($_POST);
//                               echo "</pre>";

                       unset($totjumlah);unset($persenTot);
                       foreach ($_POST as $key => $value) {
                           foreach ($_POST as $idkey => $idvalue) {
                               if(substr($idkey,0,4)=='idke'){
                                   if($key=='jumlah'.$idvalue){
                                       $totjumlah +=$value;
                                   }
                               }
                           }
                       }
                       //echo "Total ".$totjumlah;
                       $persenTot=@(($totjumlah*10)/100);
                       $totplusppn=$totjumlah+$persenTot;
                       $toleransiP=10000000;//10000000

                       if($totplusppn >= $toleransiP){
                           if(($orgIn4st == '7000' && $cektahun==2017) || $orgIn4st == '5000'){ //perubahan pajak KSO tiket 26432
                               if($kepalapjak=='010'){
                                    //exit;
                                    $user_name_cek_id=$_SESSION['user_name'];
                                    if($user_name_cek_id != ""){
                                        $action=$_POST['action'];
                                        include ('formula.php');
                                    }
                                }else{
                                $show_ket ="Silahkan Periksa kembali No Pajak Expeditur !!! Harus 010"."<br>";
                           }
                           }else{
                               if($kepalapjak=='030'){
                                    //exit;
                                    $user_name_cek_id=$_SESSION['user_name'];
                                    if($user_name_cek_id != ""){
                                        $action=$_POST['action'];
                                        include ('formula.php');
                                    }
                               }else{
                                    $show_ket ="Silahkan Periksa kembali No Pajak Expeditur !!! Harus 030"."<br>";
                               }
                           }

                       }else{
                           if($kepalapjak=='010'){
                                //exit;
                                $user_name_cek_id=$_SESSION['user_name'];
                                if($user_name_cek_id != ""){
                                    $action=$_POST['action'];
                                    include ('formula.php');
                                }
                           }else{
                                $show_ket ="Silahkan Periksa kembali No Pajak Expeditur !!! Harus 010"."<br>";
                           }
                       }

                    }else{
                        $show_ket ="Silahkan Periksa kembali Tanggal Pajak dan No Pajak Expeditur !!! (".$pesansap.")"."<br>";
                    }
//                               echo "<pre>";
//                               print_r($arrayu);
//                               echo "</pre>";
            }else{
                //exit;
                $user_name_cek_id=$_SESSION['user_name'];
                if($user_name_cek_id != ""){
                     $action=$_POST['action'];
                     include ('formula.php');
                }
            }


        }else{
            $show_ket ="Silahkan Isi Tanggal Pajak dan No Pajak Expeditur !!!";
        }

        $total=0;
}

?>
<script language=javascript>
<!-- Edit the message as your wish -->

var message="You dont have permission to right click";

function clickIE()

{if (document.all)
{(message);return false;}}

function clickNS(e) {
if
(document.layers||(document.getElementById&&!document.all))
{
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers)
{document.captureEvents(Event.MOUSEDOWN);document.  onmousedown=clickNS;}
else
{document.onmouseup=clickNS;document.oncontextmenu  =clickIE;}

document.oncontextmenu=new Function("return false")

</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Aplikasi SGG Online: Input Cost Claim :)</title><script language="JavaScript" type="text/javascript" src="../include/calendar/arsip.javascript.js"></script>
<script language="JavaScript" src="../include/calendar/JSCookMenu_mini.js" type="text/javascript"></script>
<!-- import the calendar script -->
<script type="text/javascript" src="../include/calendar/calendar_mini.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="../include/calendar/lang/calendar-en.js"></script>
<script language="JavaScript" type="text/javascript" src="../include/scrollabletable.js"></script>
<link href="../include/calendar/calendar-mos.css" rel="stylesheet" type="text/css">
<link href="../Templates/css/template_css.css" rel="stylesheet" type="text/css" />
<link href="../Templates/css/admin_login.css" rel="stylesheet" type="text/css" />
<link href="../Templates/css/theme.css" rel="stylesheet" type="text/css" />


<link rel="stylesheet" href="../include/bootstrap/css/bootstrap.min.css">
<script src="../include/jquery.min.js"></script>
<script src="../include/bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="../include/bootstrap/css/bootstrap-cus.css">
<script type="text/javascript">
checked=false;
function checkedAll (frm1) {
	var aa= document.getElementById('fsimpan');
	 if (checked == false)
          {
           checked = true
		   markAllRows('fsimpan');
          }
        else
          {
          checked = false
		  unMarkAllRows('fsimpan')
          }
/*	for (var i =0; i < aa.elements.length; i++)
	{
	 aa.elements[i].checked = checked;

	}
*/ }

function cek_org(id_cek) {
    var obj = document.getElementById(id_cek);
    var cek = obj.value;
    var satu_data = "0";
    for (var keb = 0; keb < cek; keb++){
        var rowke = 'idke'+keb;
        var com_rowke = document.getElementById(rowke);
        if (com_rowke.checked == true)  {
            satu_data = "1";
        }
    }
    var cekdata = 0; var kec1; var kec2;
    for (var keb1 = 0; keb1 < cek; keb1++){
        kec1 = keb1 ;
        for (var keb2 = 0; keb2 < cek; keb2++){
            kec2 = keb2;
            var rowke = 'idke'+kec1;
            var com_rowke = document.getElementById(rowke);

            var rowke2 = 'idke'+kec2;
            var com_rowke2 = document.getElementById(rowke2);

            var rowke = 'orgke'+kec1;
            var com_roworg1 = document.getElementById(rowke);

            var rowke2 = 'orgke'+kec2;
            var com_roworg2 = document.getElementById(rowke2);

            if (com_rowke.checked == true && com_rowke2.checked == true)  {

                 if (com_roworg1.value != com_roworg2.value ) {
                        cekdata++;

                }
            }
        }
    }

    if (parseInt(cekdata) > 0) {
        alert("ORG yang dipilih harus sama ...");
        return document.hasil = false;
    }
    if (satu_data == "0") {
        alert("Minimal Pilih Satu Data...");
        return document.hasil = false;
    }
    return document.hasil = true;
}

function markAllRows( container_id ) {
    var rows = document.getElementById(container_id).getElementsByTagName('tr');
    var checkbox;

    for ( var i = 0; i < rows.length; i++ ) {

        checkbox = rows[i].getElementsByTagName( 'input' )[0];

        if ( checkbox && checkbox.type == 'checkbox' ) {
			if (checkbox.checked != true){
				checkbox.checked = true;
				rows[i].className += ' selected';
			}
        }
    }

    return true;
}

function unMarkAllRows( container_id ) {
    var rows = document.getElementById(container_id).getElementsByTagName('tr');
    var checkbox;

    for ( var i = 0; i < rows.length; i++ ) {

        checkbox = rows[i].getElementsByTagName( 'input' )[0];

        if ( checkbox && checkbox.type == 'checkbox' ) {
			if (checkbox.checked != false){
			checkbox.checked = false;
            rows[i].className = rows[i].className.replace(' selected', '');
			}
        }
    }

    return true;
}

</script>

<style type="text/css">
body	{background:#fff;}
table	{border:0;border-collapse:collapse;}
td		{padding:4px;}
tr.odd1	{background:#F9F9F9;}
tr.odd0	{background:#FFFFFF;}
tr.highlight	{background:#BDA9A2;}
tr.selected		{background:orange;color:#fff;}
</style>

<script type="text/javascript">

function IsNumeric(obj,panjang)
   //  check for valid numeric strings
   {
   var strValidChars = "0123456789";
   var strChar;
   var strString = obj.value;
   if (strString.length != panjang){
     alert("Isi dengan Angka " + panjang + " digit..");
	 obj.value="";
	 return false;
	} else {
	   //  test strString consists of valid characters listed above
	   for (i = 0; i < strString.length; i++)
		  {
		  strChar = strString.charAt(i);
		  if (strValidChars.indexOf(strChar) == -1)
			 {
			 alert("Hanya Masukkan Angka 0-9 dengan " + panjang + " digit..");
			 obj.value="";
			 return false;
			 }
		  }
	 }
   }


function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

function addClass(element,value) {
  if (!element.className) {
    element.className = value;
  } else {
    newClassName = element.className;
    newClassName+= " ";
    newClassName+= value;
    element.className = newClassName;
  }
}


function stripeTables() {
	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "pickme") {
			var tbodies = tables[m].getElementsByTagName("tbody");
			for (var i=0; i<tbodies.length; i++) {
				var odd = true;
				var rows = tbodies[i].getElementsByTagName("tr");
				for (var j=0; j<rows.length; j++) {
					if (odd == false) {
						odd = true;
						addClass(rows[j],"odd1");
					} else {
						addClass(rows[j],"odd0");
						odd = false;
					}
				}
			}
		}
	}
}
function highlightRows() {
  if(!document.getElementsByTagName) return false;
  	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "pickme") {
			  var tbodies = tables[m].getElementsByTagName("tbody");
			  for (var j=0; j<tbodies.length; j++) {
				 var rows = tbodies[j].getElementsByTagName("tr");
				 for (var i=0; i<rows.length; i++) {
					   rows[i].oldClassName = rows[i].className
					   rows[i].onmouseover = function() {
						  if( this.className.indexOf("selected") == -1)
							 addClass(this," highlight");
					   }
					   rows[i].onmouseout = function() {
						  if( this.className.indexOf("selected") == -1)
							 this.className = this.oldClassName
					   }
				 }
			  }
		}
	}
}

function selectRowCheckbox(row) {
	var checkbox = row.getElementsByTagName("input")[0];
	if (checkbox.checked == true) {
		checkbox.checked = false;
	} else
	if (checkbox.checked == false) {
		checkbox.checked = true;
	}
}

function lockRow() {
  	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "pickme") {
				var tbodies = tables[m].getElementsByTagName("tbody");
				for (var j=0; j<tbodies.length; j++) {
					var rows = tbodies[j].getElementsByTagName("tr");
					for (var i=0; i<rows.length; i++) {
						rows[i].oldClassName = rows[i].className;
						rows[i].onclick = function() {
							if (this.className.indexOf("selected") != -1) {
								this.className = this.oldClassName;
							} else {
								addClass(this," selected");
							}
							selectRowCheckbox(this);
						}
					}
				}
		}
	}
}

addLoadEvent(stripeTables);
addLoadEvent(highlightRows);
addLoadEvent(lockRow);


function lockRowUsingCheckbox() {
	var tables = document.getElementsByTagName("table");
	for (var m=0; m<tables.length; m++) {
		if (tables[m].className == "pickme") {
			var tbodies = tables[m].getElementsByTagName("tbody");
			for (var j=0; j<tbodies.length; j++) {
				var checkboxes = tbodies[j].getElementsByTagName("input");
				for (var i=0; i<checkboxes.length; i++) {
					checkboxes[i].onclick = function(evt) {
						if (this.parentNode.parentNode.className.indexOf("selected") != -1){
							this.parentNode.parentNode.className = this.parentNode.parentNode.oldClassName;
						} else {
							addClass(this.parentNode.parentNode," selected");
						}
						if (window.event && !window.event.cancelBubble) {
							window.event.cancelBubble = "true";
						} else {
							evt.stopPropagation();
						}
					}
				}
			}
		}
	}
}
addLoadEvent(lockRowUsingCheckbox);

function findplant(org) {
		var com_org = document.getElementById('org');
		var strURL="cari_plant.php?org="+com_org.value;
		popUp(strURL);
}
function ketik_plant(obj) {
	var com=document.getElementById('org');
	var nilai_tujuan =obj.value;
	var cplan=document.getElementById('nama_plant');
	cplan.value = "";
	var strURL="ketik_plant.php?org="+com.value+"&plant="+nilai_tujuan;
	var req = getXMLHTTP();
	if (req) {
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				// only if "OK"
				if (req.status == 200) {
					document.getElementById('plantdiv').innerHTML=req.responseText;
				} else {
					alert("There was a problem while using XMLHTTP:\n" + req.statusText);
				}
			}
		}
		req.open("GET", strURL, true);
		req.send(null);
	}
}
function kodepajak(val){
    if((val.value!='010')&&(val.value!='030')){
        alert("No Pajak Expeditur dengan awalan 010 atau 030");
        val.value='';
        val.select();
        val.focus();
        return false;
    }else{
	return true;
    }

}

function find_rek() {
    var no_vendor = document.getElementById("no_vendor");
    var strURL="cari_rek.php?no_vendor="+no_vendor.value;
    popUp(strURL);
}
</script>

</head>

<body>
<script type="text/javascript" language="JavaScript">
	//ini ni yang buat div tapi kita hidden... ocre....
	document.write('<div id="tunggu_ya" style="display:none" ><table width="100%" height="95%" align="center" valign="middle"><tr><td width="100%" height="100%" align="center" valign="middle"><h3>Loading Data....<br><br><div align="center"><img src="../images/loading.gif"></img></div></h3></td></tr></table></div>');

	</script>
<div id="halaman_tampil" style="display:inline">

<div align="center">
<table width="600" align="center" class="adminheading" border="0">
<tr>
<th class="kb2">Create Invoice Klaim Darat </th>
</tr></table></div>
<?
	if($total<1){
?>

<div align="center">
<table width="600" align="center" class="adminlist">
<tr>
<th align="left" colspan="4"> &nbsp;Form Search Cost Claim </th>
</tr>
</table>
</div>

<form id="form1" name="form1" method="post" action="create_invoice_bag_darat.php" onSubmit="validasi('bulan','','R','tahun','','RisNum','vendor','','R','warna_plat','','R');return document.hasil">
  <table width="600" align="center" class="adminform">
    <tr width="174">
      <td class="puso">&nbsp;</td>
      <td class="puso">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr width="174">
      <td class="puso">No SPJ </td>
      <td class="puso">:</td>
      <td><input type="text" id="no_shipment" name="no_shipment" value="<?=$no_shipment?>"/></td>
    </tr>
    <tr>
      <td  class="puso">Distributor</td>
      <td  class="puso">:</td>
      <td ><input type="text" id="distributor" name="distributor"  value="<?=$distributor?>" /></td>
    </tr>
    <tr>
      <td  class="puso">Periode Shipment </td>
      <td  class="puso">:</td>
      <td ><select name="bulan" id="bulan">
        <option value="">---Pilih---</option>
        <? $fungsi->ex_bulan($bulan);?>
      </select> <input type="text" id="tahun" name="tahun"  value="<?=$tahun?>" maxlength="4" size="10"/>
      *</td>
    </tr>

    <tr>
      <td  class="puso">Warna Plat </td>
      <td  class="puso">:</td>
      <td >
	<!--  <script type="text/javascript">
		function display_div(show){
		   document.getElementById('HITAM').style.display = "none";
		   document.getElementById(show).style.display = "block";
		}
	  </script>
	  <select name="warna_plat" id="warna_plat">
          <option value=""onClick="display_div('');">---Pilih---</option>
          <? $fungsi->ex_warna_plat($warna_plat);?>
        </select> -->

		<script type="text/javascript">
          function display_div(show){
          document.getElementById('HITAM').style.display = "none";
          document.getElementById(show).style.display = "block";
          }
        </script>

             <select name="warna_plat" id="warna_plat">
             <option selected="selected"> </option>
             <option onClick="display_div('');">----Pilih---</option>
             <option onClick="display_div('HITAM');">HITAM</option>
             <option onClick="display_div('HITAM');">KUNING</option>
			 </select>
        *</td>
    </tr>
	<tr>
	<td  class="puso">Termin Invoice </td>
    <td  class="puso">:</td>
	<td>
	        <div id="HITAM" style="display:none;">
			<select name="termin" id="termin">
            <option value="">---Pilih---</option>
		    <option value="1">Termin 1</option>
		    <option value="2">Termin 2</option>
		    <option value="3">Termin 3</option>
		    <option value="4">All Termin</option>
            </select></div>
	  </td>
	</TR>
   <tr width="174">
      <td class="puso">&nbsp;</td>
      <td class="puso">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ThemeOfficeMenu">&nbsp;</td>
      <td class="ThemeOfficeMenu">&nbsp;</td>
    <td rowspan="2"><input name="cari" type="submit" class="button" id="cari" value="Find" />    </tr>
    <tr>
      <td class="ThemeOfficeMenu">&nbsp;</td>
      <td class="ThemeOfficeMenu">&nbsp;</td>
    </tr>
  </table>
</form>
<? } ?>
<br />
<br />
<?
	if($total>0){
$filhpajak='';
if ($warna_plat == "HITAM"){
//@liyantanto
$filhpajak="javascript:kodepajak(pjk1);";

?>
<form id="fsimpan" name="fsimpan" method="post" action="<?=$page;?>" onSubmit="validasi('pjk1','','R','pjk2','','R','pjk3','','R','pjk4','','R','tanggal_pjk','','R','no_rek','','R');cek_org('total');return document.hasil">
<?
} else {
?>
<form id="fsimpan" name="fsimpan" method="post" action="komentar.php" onSubmit="validasi('no_invoice_vendor','','R');cek_org('total');return document.hasil">

<? } ?>
	<div align="center">
	<table width="95%" align="center">
    <tr>
      <td width="13%"  class="puso">No Kwitansi Expeditur </td>
      <td width="1%"  class="puso">:</td>
      <td width="86%" ><div align="left">
        <input type="text" id="no_kwitansi_vendor" name="no_kwitansi_vendor" value="" size="50" maxlength="20"/>
      </div></td>
    </tr>
    <tr>
      <td width="13%"  class="puso">No Invoice Expeditur </td>
      <td width="1%"  class="puso">:</td>
      <td width="86%" ><div align="left">
        <input type="text" id="no_invoice_vendor" name="no_invoice_vendor" value="" size="50" maxlength="20"/> *boleh tidak diisi
      </div></td>
    </tr>
	</table>
	<? if ($warna_plat == "HITAM"){?>

	<table width="95%" align="center">
    <tr>
      <td width="13%"  class="puso">No Pajak Expeditur </td>
      <td width="1%"  class="puso">:</td>
      <td width="86%" ><div align="left">
        <input type="text" id="pjk1" name="pjk1" value="" size="10" maxlength="3" onBlur="javascript:IsNumeric(this,'3');<?=$filhpajak;?>"/>
        <strong>.</strong>
		<input type="text" id="pjk2" name="pjk2" value="" size="10" maxlength="3" onBlur="javascript:IsNumeric(this,'3')"/>
		<strong>-</strong>
		<input type="text" id="pjk3" name="pjk3" value="" size="10" maxlength="2" onBlur="javascript:IsNumeric(this,'2')"/>
		<strong>.</strong>
		<input type="text" id="pjk4" name="pjk4" value="" size="20" maxlength="8" onBlur="javascript:IsNumeric(this,'8')"/>
      </div></td>
    </tr>
            <? } ?>
    <table width="95%" align="center">
      <tr>
        <? if ($warna_plat == "HITAM"){?>
          <td width="13%"  class="puso">Tanggal Pajak </td>
        <? } else { ?>
      <td width="13%"  class="puso">Tanggal</td>
       <? }?>
      <td width="1%"  class="puso">:</td>
      <td width="86%" ><input name="tanggal_pjk" type="text" id="tanggal_pjk" <?=$hanyabaca?> value="<?=$tanggal_pjk?>" />
          <input name="btn_pjk" type="button" class="button" onClick="return showCalendar('tanggal_pjk');" value="..." />

	  </td>
    </tr>

    <tr>
      <input name="no_vendor" id="no_vendor" type="hidden" value="<?=$no_vendor_v;?>" />
      <td  class="puso">No Rekening </td>
      <td  class="puso">:</td>
      <td > <input type="text" id="bvtyp" name="bvtyp"  value="" readonly="true" size="8"/> &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="nama_bank" name="nama_bank"  value="" readonly="true"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="no_rek" name="no_rek"  value="" readonly="true"/>
      <input name="cari_rek" type="button" class="button" id="cari_rek" value="..." onClick="find_rek()"/></td>
    </tr>
    <tr>
      <td  class="puso">&nbsp;</td>
      <td  class="puso">&nbsp;</td>
    <td >
      <input type="text" id="cabang_bank" name="cabang_bank"  value="" readonly="true" size="50"/>
    </td>
    </tr>
	</table>

	</div>
	<div align="center">
	<table width="95%" align="center" class="adminlist">
	<tr>
	<th align="left" colspan="4"><span class="style5">&nbsp;Tabel Data Invoice Claim </span></th>
	</tr>
	</table>
	</div>
	<div align="center">
	<table id="myScrollTable" width="95%" align="center" class="pickme">
	<thead >
	  <tr class="quote">
		<td ><strong><input type="button" class="button" onClick="checkedAll('fsimpan');" value="CEK ALL"></strong></td>
                <td align="center"><strong >Org </strong></td>
		<td align="center"><strong >TGL. SPJ </strong></td>
		<td align="center"><strong >AREA. LT </strong></td>
		<td align="center"><strong >NO. SPJ </strong></td>
		 <td align="center"><strong>NO. POL </strong></td>
		 <td align="center"><strong>PRODUK</strong></td>
		 <td align="center"><strong>DISTRIBUTOR</strong></td>
		 <td align="center"><strong>K.KTG</strong></td>
		 <td align="center"><strong>K.SMN</strong></td>
		 <td align="center"><strong>KWANTUM</strong></td>
		 <td align="center"><strong>TARIF</strong></td>
		 <td align="center"><strong>JUMLAH</strong></td>
      </tr >
	  </thead>
	  <tbody >
  <?  for($i=0; $i<$total;$i++) {

		$b=$i+1;
		$rowke="rowke".$i;
		$idke="idke".$i;
		$appke=$id_v[$i];
		$urutke="urutke".$i;
                $orgCom="orgke".$i;
                $org_fpajakkk=$com[$i];
		?>
		<tr>
		<td align="center"><input name="<?=$idke;?>" id="<?=$idke;?>" type="checkbox" value="<?=$appke;?>" /> <? echo $b; ?></td>
                <td align="center"><? echo $com[$i]; ?><input name="<?=$orgCom;?>" id="<?=$orgCom;?>" type="hidden" value="<?=$com[$i];?>" /></td>
		<td align="center"><? echo $tgl_kirim_v[$i]; ?></td>
		<td align="center"><? echo $sal_distrik_v[$i]; ?></td>
		<td align="center"><? echo $no_shipment_v[$i]; ?></td>
		<td align="center"><? echo $no_pol_v[$i]; ?></td>
		<td align="center"><? echo $produk_v[$i]; ?></td>
		<td align="center"><? echo $sold_to_v[$i]." / ".$nama_sold_to_v[$i]; ?></td>
		<td align="center"><? echo number_format($qty_kantong_rusak_v[$i],0,",","."); ?></td>
		<td align="center"><? echo number_format($qty_semen_rusak_v[$i],0,",","."); ?></td>
		<td align="center"><? echo number_format($qty_v[$i],0,",","."); ?></td>
		<td align="center"><? echo number_format($tarif_cost_v[$i],0,",","."); ?></td>
		<td align="center">
                    <? echo number_format($shp_cost_v[$i],0,",","."); ?>
                    <input name="jumlah<?=$appke;?>" type="hidden" id="jumlah<?=$appke;?>" size="10" value="<?=$shp_cost_v[$i];?>" readonly="true"/>
                </td>
		</tr>
	  <? } ?>
	  </tbody>
	  <tfoot>
	  <tr class="quote">
		<td colspan="13" align="center">
		<input name="simpan" type="submit" class="button" id="simpan" value="Save" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="create_invoice_bag_darat.php" target="isi" class="button">Cancel</a>
		<input name="total" id="total" type="hidden" value="<?=$total;?>" />
		<input name="no_vendor" type="hidden" value="<?=$no_vendor_v;?>" />
		<input name="nama_vendor" type="hidden" value="<?=$nama_vendor_v;?>" />
		<input name="warna_plat" type="hidden" value="<?=$warna_plat;?>" />
		<input name="bulan" type="hidden" value="<?=$bulan;?>" />
		<input name="tahun" type="hidden" value="<?=$tahun;?>" />
		<input name="spt_cek" type="hidden" value="<?=$spt_cek;?>" />
		<input name="tgl_tremin" type="hidden" value="<?=$tgl_tremin;?>" />
                <input name="org_fpajak" type="hidden" value="<?=$org_fpajakkk;?>" />
		<input name="termin" type="hidden" value="<?=$termin;?>" />
		<input name="action" type="hidden" value="create_invoice_bag_darat" />		 </td>
	    </tr>
	  </tfoot>
	</table>
	</div>
	<?
	}?>
<div align="center">
<?
echo $komen;

?></div>
</form>
<!--<div class="warning message">
<h3>Warning!</h3>
<p>Invoice PPL dengan tahun pengiriman SPJ <span style="color:white;">dibawah tahun 2017</span>,</br>
mohon tidak dibuat dalam satu inovice dengan pengiriman diatas <span style="color:white;">tahun 2017 !!!</span></p>
</div>-->
<p>&nbsp;</p>

<? if ($total> 11){ ?>
<script type="text/javascript">
var t = new ScrollableTable(document.getElementById('myScrollTable'), 300);
</script>
<? } ?>
</p>
</div>

<?if($show_ket!=''){?>
<div align="center" class="login">
<?
echo $show_ket;
?>
</div>
<?}?>

<? //include ('../include/ekor.php'); ?>
	<script language=javascript>
	//We write the table and the div to hide the content out, so older browsers won't see it
		obj=document.getElementById("tunggu_ya");
		obj.style.display = "none";
		obj_tampil=document.getElementById("halaman_tampil");
		obj_tampil.style.display = "inline";
	</script>

</body>
</html>
