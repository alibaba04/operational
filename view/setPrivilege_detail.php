<?php 
// AUTHOR: Ng Kho Kim Fang
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
$curPage="html/setPrivilege_detail";

//Periksa hak user pada modul/menu ini
if($_SESSION["my"]->id=="")
{
	session_unregister("my");
	header("Location: index.php");
	exit;
}

if (substr($_SERVER['PHP_SELF'],-10,10)=="index2.php" && $_POST["rdoCabang"]!="")
{	
       
        $kodeCabang=secureParam($_POST["rdoCabang"], $dbLink);
        $q = "SELECT c.Nama, c.KodeCabang FROM Cabang c  WHERE c.KodeCabang='".$kodeCabang."' ;";
	$rsCabang = mysql_query($q, $dbLink);
	if($cabang=mysql_fetch_row($rsCabang))
        {
            require_once('./class/c_user.php');
            $_SESSION["my"] = new c_user($_SESSION["my"]->id, $_SESSION["my"]->name, $_SESSION["my"]->groups, $_SESSION["my"]->menus, $_SESSION["my"]->privilege, $cabang["0"], $cabang["1"]);
        }
        header("Location:index.php");
	exit;
}
?>
<form action="index2.php?page=html/setPrivilege_detail" method="post" name="frm">
	<div align="center" class="title-01">PILIH CABANG</div>
	<input type='hidden' name='txtMode' id='txtMode' value='Add'>	
	<div class="pesan"><?=$_GET["pesan"];?></div>
  	<table width="100%" border="0">	  
	<?php
	$user = secureParam($_SESSION["my"]->id, $dbLink);
	$q = "SELECT c.Nama, c.KodeCabang FROM UserCabang u INNER JOIN Cabang c ON u.KodeCabang=c.KodeCabang 
		  WHERE u.KodeUser='".$user."' ORDER BY c.Nama;";
	$rsCabang = mysql_query($q, $dbLink);
	while ($cabang=mysql_fetch_row($rsCabang))
	{
	?>
	  <tr> 
		<td><div align="right" class="text1"> <input type="radio" name="rdoCabang" value="<?=$cabang[1];?>" /></div></td>
		<td><?=$cabang[0];?></td>
	  </tr>
	<?php
	}
	?>
	  <tr> 
		<td height="40" align="right" valign="middle"><div align="left"> 
		<?php
		  echo '<input type="image" value="Submit" src="image/saveBig.png"  width="32" height="32" Submit>';
		?>
		</div></td>
	  </tr>
	</table>
</form>
