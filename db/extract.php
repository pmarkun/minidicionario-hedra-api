<?php
/**
 * extract.php - extrai dados do banco de dados salvando no formato TeX desejado 
 *
 * @author Marcelo C. de Freitas <marcelo@kow.com.br>
 * @when Março de $rev011
 */


/******************/
/* Configurações */
/****************/
mysql_connect( "localhost", "root", "senharoot" );
mysql_select_db( "dicionario_db_latin1_2" );





/************/
/* Funções */
/**********/


/**
 * Contrói a query SQL desejada
 */

function getQuery( $letra, $limit ) {
	$rev = "2"; // pegando dados da 2a revisão
	$sql_core = "select " .
				"VerbetesNovo.Verbete as Verbete,".
				"DefinicoesNovo.DefinicaoPronunciaRevisor$rev as Pronuncia, ".
				"Rubricas.Rubrica as Rubrica, ".
				"DefinicoesNovo.DefinicaoPluralRevisor$rev as Plural, ".
				"DefinicoesNovo.DefinicaoPluralPronunciaRevisor$rev as PluralPronuncia, ".
				"DefinicoesNovo.DefinicaoFemininoRevisor$rev as Feminino, ".
				"DefinicoesNovo.DefinicaoFemininoPronunciaRevisor$rev as FemininoPronuncia, ".
				"ClasseGramatical.ClasseGramatical as ClasseGramatical, ".
				"AcepcoesNovo.AcepcaoRevisor$rev as Acepcao, ".
				"AcepcoesNovo.Abonacao as Abonacao,".
				"VerbetesNovo.VerbeteSeparacaoSilabicaRevisor$rev as VerbeteSeparacaoSilabica,".
				"AcepcoesNovo.idConjugacaoRevisor$rev as idConjugacao".
			" from ".
				"AcepcoesNovo, ".
				"ClasseGramatical, ".
				"DefinicoesNovo, ".
				"Rubricas, ".
				"VerbetesNovo ".
			" where ".
				"VerbetesNovo.id = DefinicoesNovo.idVerbete and ".
				"AcepcoesNovo.idDefinicao = DefinicoesNovo.id and ".
				"AcepcoesNovo.idRubricaRevisor$rev = Rubricas.id and ".
				"AcepcoesNovo.idClasseGramaticalRevisor$rev = ClasseGramatical.id";


	$sql_order = "order by VerbetesNovo.Verbete";
	$sql_letra = " and VerbetesNovo.Verbete like \"" . $letra ."%\" ";

	$query = $sql_core . $sql_letra . $sql_order;

	if(  $limit != null ) {
		$query .=" limit $limit";
	}

	return $query;
}


/**
 * inicializa um bloco de verbete
 */
function beginVerbete( $f ){
	fwrite( $f, "\\verb" );
}


function limpa( $val ) {
	// TODO :: regras =>
	//		<i></i>	=> \textit{}
	//		resto	=> strip_tags




	$from = array(
			"/<i>/",
			"/<b>/",
			"/<sup>/",
			"/<\/.*?>/msi"
		);

	$to = array(
			"\\textit{",
			"\\textbf{",
			"\\textsuperscript{",
			"}"
		);

	$fromStrReplace = array(
			"",
			"",
			"\n",
			"#",
			"%"
		);
	$toStrReplace = array(
			"",
			"",
			" ",
			"\\#",
			"\\%"
		);


	return str_replace( $fromStrReplace, $toStrReplace, strip_tags( preg_replace( $from, $to, $val ) ) ); 
}


/**
 * Imprime um parâmetro no arquivo de acordo com a chave formatado de acordo com LaTeX.
 *
 * $f	arquivo de saída
 * $r	resultado de query SQL como em mysql_fetch_array
 * $key	 a chave
 */
function parametro( $f, $r, $key, $extraKey = null ){

	if( isset( $r[$key] ) ) {
		fwrite( $f, "{".limpa( $r[$key] ) );
		if( $extraKey != null && isset( $r[$extraKey] ) && $r[$extraKey] != "")
			fwrite( $f, "\\textit{".limpa( $r[$extraKey ] )."}" );
		fwrite( $f, "}" );
	} else {
		echo "FALTA :: $key\n";
		fwrite( $f, "{}" );
	}
}


function parametroPronuncia( $f, $r, $key ) {

	$pkey = $key."Pronuncia";
	if( !isset( $r[$key] ) )
		die( "Falta $key!" );
	if( !isset( $r[$pkey] ) )
		die( "Falta $pkey!" );

	$valor = limpa( $r[$key] );
	$pronuncia = limpa( trim( $r[$pkey] ) );

	if( $pronuncia != null && $pronuncia != "" ) {
		$valor .= " ⟨".$pronuncia."⟩";
	}

	fwrite( $f, "{".$valor."}" );

}


/**
 * finaliza um comando de verbete
 */
function endVerbete( $f ) {
	fwrite( $f, "\n" );
}




function processaLetra( $letra, $limit ) {

	$sql = getQuery( $letra, $limit );
	$q = mysql_query( $sql ) or die( mysql_error() );

	if( $limit != null )
		$f = fopen( "saida_exemplo/$letra.tex", "w" );
	else
		$f = fopen( "saida/$letra.tex", "w" );

	while( $r = mysql_fetch_array( $q ) ) {
		beginVerbete($f);
			parametro( $f, $r, "Verbete" );
			parametro( $f, $r, "Pronuncia" );
			parametro( $f, $r, "Rubrica" );	// aka Área do conhecimento
			parametroPronuncia( $f, $r, "Plural" );
			parametroPronuncia( $f, $r, "Feminino" );
			parametro( $f, $r, "ClasseGramatical" );
			parametro( $f, $r, "Acepcao", "Abonacao" );
			parametro( $f, $r, "VerbeteSeparacaoSilabica" );
			parametro( $f, $r, "idConjugacao" );
		endVerbete($f);
	}
	fclose( $f );

}



/***************/
/* Algorítimo */
/*************/


for( $i = ord("a"); $i <= ord( "z" ); $i++ ) {
	$letra = chr($i);

	echo "Extraindo $letra \n";
	processaLetra( $letra, null );
	echo "Extraindo dump de exemplo para $letra\n";
	processaLetra( $letra, 100 );
}



?>
