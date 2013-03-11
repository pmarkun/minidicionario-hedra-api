<?
/**
 * Corrige erros do tipo </i>lala<i>
 */




mysql_connect( "localhost", "root", "senharoot" );

mysql_select_db( "dicionario_db_latin1_2" );


$campo  = "AcepcaoRevisor2";
$tabela = "AcepcoesNovo";
$commit = false;


$query = "SELECT id,$campo from $tabela where $campo like \"%<\/i>%<i>%\" and $campo not like \"%<i>%<\/i>%\"";



$q = mysql_query( $query ) or die (mysql_error() );


while( $r = mysql_fetch_array($q) ) {
	$novoValor = preg_replace( "/<\/i>([^<]*?)<i>/i", "<i>$1</i>", $r[$campo] );
	$query = "UPDATE $tabela set $campo=\"$novoValor\" where id=".$r["id"];


	if($commit) {
		echo "Rodando $query\n\n";
		mysql_query( $query ) or die( mysql_error() );
	} else {
		echo $r[$campo]."\n".$query."\n\n";
	}
}


