<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$src=explode("/", $_GET['src']);
if(count($src)==0) {
	$src[1]=$src[0];
	$src[0]="tables";
}

$columnDefination=[];
switch ($src[0]) {
	case 'tables':
		$columnDefination=_db($dbKey)->get_defination($src[1]);
		break;
	
	default:
		echo "<h5 align=center>Sorry, viewing data for type <b>{$src[0]}</b> is not supported yet</h5>";
		return;
		break;
}
//var_dump($columnDefination);

?>
<div class='col-xs-12' style='margin-top: 20px;'>
<form id='dataInsertForm'>
	<?php
		$autoFillColumns=array_keys($autoFillColumns);
		foreach ($columnDefination as $column) {
			if(in_array($column[0], $autoFillColumns)) {
				continue;
			}

			$name=strtoupper($column[0]);
			$id=md5($name);
			$required="";
			

			if($column[5]=="auto_increment" && $column[3]=="PRI") {
				continue;
			}
			if($column[2]=="YES") {
				$required="required";
			}
			
			$html="<div class='form-group row {$required}'>";
			if($required) {
				$html.="<label for='{$id}' class='col-sm-3 form-control-label'>{$name} <citie>*</citie></label>";
			} else {
				$html.="<label for='{$id}' class='col-sm-3 form-control-label'>{$name} </label>";
			}
			$html.="<div class='col-sm-9'>";
			
			$html.=getInputBlock($name,$required,$column);

			$html.="</div>";
			$html.="</div>";

			echo $html;
		}
	?>
	<hr>
	<div class='text-center'>
		<button type='reset' class='btn btn-danger'>Reset</button>
		<button type='submit' class='btn btn-success'>Submit</button>
	</div>
</form>
</div>
<script>
$(function() {
	$("#dataInsertForm").submit(function() {
		if($("#dataInsertForm").valid()) {
			q=$("#dataInsertForm").serialize();
			lx=_service("dbEdit","insertRecord")+"&src=<?=$_GET['src']?>";
			processAJAXPostQuery(lx,q,function(txt) {
				if(txt=="success") {
					$("#dataInsertForm")[0].reset();
					lgksToast("Data inserted successfully");
				} else {
					lgksToast(txt);
				}
			});
		}
		return false;
	});
});
</script>