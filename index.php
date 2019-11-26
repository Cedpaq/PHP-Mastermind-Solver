<?php
function array_random($array, $amount = 1)
{
    $keys = array_rand($array, $amount);

    if ($amount == 1) {
        return $array[$keys];
    }

    $results = [];
    foreach ($keys as $key) {
        $results[] = $array[$key];
    }

    return $results;
}

function result_line($msg, $bons=null)
{
	global $results;
	global $try_count;
	global $win_case;

	$try_count = !isset($try_count) ? 1 : $try_count;

	if( $msg == 'WIN' )
	{
		$results[] = '<tr>
		<td  align="center" colspan="6" style="color: #cc0000;"><h1>Code trouvé en '.$try_count.' coups!</h1>
		</td>
		<td>
			'.$win_case.'
		</td>
		</tr>';
	}
	else
	{
		$results[] = '<tr><td align="center">'.(!is_null($bons) ? (count($results)) : '').'</td><td colspan="5" style="background-color: #e7e7e7">'.$msg.'</td><td align="center">'.$bons.'</td></tr>';
	}
	
}

function get_change( $v1, $v2 )
{
	if( is_null($v1) ) return '';
	if( ($v1-$v2) < 0 )
	{
		$change = ((-1)*($v1-$v2));
		if( $change === 0 ) return '';
		$change = '<strong style="color: green">+'.$change.'</strong>';
	}
	else
	{
		$change =  ($v1-$v2);
		if( $change === 0 ) return '';
		$change = '<strong style="color: #cc0000">-'.$change.'</strong>';
	}

	return '('.$change.')';
	#return ' <strong>('.(($v1-$v2) < 0 ? '+'. : '-'.).')</strong>';
}

function result_color_line($try, $bons=null)
{
	global $results;
	global $still_colors;
	global $bad_colors;
	global $good_colors;
	global $last_bons;
	global $mystere_colors;
	global $bad_default_colors;
	global $debug;

	$change = get_change($last_bons, $bons);

	$winner='';
	if( $bons == 5 )
	{
		$winner = 'winner';
	}

	$debug_tr='';
	if($debug) 
	{
		$debug_tr = '<tr>
		<td colspan="7" align="">
			À tester: <strong>'.implode(', ', $still_colors).'</strong>
		</td>
		</tr>';
	}
	$results[] = $debug_tr.'
		<tr class="'.$winner.'">
			<td align="center"><strong>'.(!is_null($bons) && count($results) != 0 ? (count($results)+1) : 'DÉPART').'</strong></td>
			<td width="100px" style="background-color: '.$try[0].'">&nbsp;</td>
			<td width="100px" style="background-color: '.$try[1].'">&nbsp;</td>
			<td width="100px" style="background-color: '.$try[2].'">&nbsp;</td>
			<td width="100px" style="background-color: '.$try[3].'">&nbsp;</td>
			<td width="100px" style="background-color: '.$try[4].'">&nbsp;</td>
			<td align="center">'.$bons." ".$change.'</td>

			<td align="center">'.implode(', ', $bad_colors).'</td>
			<td align="center">'.implode(', ', $good_colors).'</td>
			<td align="center">'.implode(', ', $mystere_colors).'</td>
			<td align="center">'.implode(', ', $bad_default_colors).'</td>
		</tr>
		';
}

function simulate_verif_bons($try)
{
	global $last_try;
	global $last_try_col;
	global $last_bons;
	global $cryptage;

	global $bad_colors;
	global $good_colors;
	global $mystere_colors;

	global $must_set_last_try_color_back;

	$bons=0;
	for( $i=0; $i<5; $i++ )
	{
		/* BONNE POSITION
		if( $last_try[$i] == $cryptage[$i] )
		{
			$bons++;
		}
		*/
		// Bonne couleur
		#var_dump($last_try);
		#var_dump($cryptage);exit;
		if( in_array($try[$i], $cryptage) )
		{
			$bons++;
		}
	}

	return $bons;
}

function verif_bons($try)
{
	global $last_try;
	global $last_try_col;
	global $last_bons;
	global $cryptage;

	global $colors;
	global $bad_colors;
	global $good_colors;
	global $mystere_colors;
	global $bad_default_colors;
	global $still_colors;

	global $win_case;
	global $winner_try;

	global $must_set_last_try_color_back;

	$bons=0;
	for( $i=0; $i<5; $i++ )
	{
		/* BONNE POSITION
		if( $last_try[$i] == $cryptage[$i] )
		{
			$bons++;
		}
		*/
		// Bonne couleur
		#var_dump($last_try);
		#var_dump($cryptage);exit;
		if( in_array($try[$i], $cryptage) )
		{
			$bons++;
		}
	}

	/*
	if( is_null($last_try) )
	{
		// On est au départ
		#$last_bons = $bons;
		$last_try = $try;
		result_color_line($try, $bons);
		return 0;
	}
	*/

	if( $last_bons !== null )
	{
		if( $bons > $last_bons )
		{
			// +1
			// Celui enlevé était pas bon
			// Le nouveau est bon
			#echo "-1 ".$last_try[ $last_try_col-1 ];

			$bad_colors[] = $last_try[ $last_try_col-1 ];
			$good_colors[] = $try[ $last_try_col-1 ];

			$good_colors = array_unique($good_colors);
			$bad_colors = array_unique($bad_colors);
		}
		else if( $bons < $last_bons )
		{
			// -1
			// Celui enlevé était bon
			// Le nouveau est pas bon
			#echo "+1 ".$last_try[ $last_try_col-1 ];
			$good_colors[] = $last_try[ $last_try_col-1 ];
			$bad_colors[] = $try[ $last_try_col-1 ];
			#echo implode(', ', $mystere_colors)."<br>";

			if( in_array($try[ $last_try_col-1 ], $bad_default_colors) )
			{
				$bad_colors = array_merge($bad_colors, $bad_default_colors);
				$bad_colors = array_unique($bad_colors);
				$bad_default_colors=[];

				foreach ($bad_colors as $key => $color) 
				{
					if (($searchkey = array_search($color, $mystere_colors)) !== false) {
					    //unset($still_colors[$key]);
					    array_splice($mystere_colors, $searchkey, 1);
					}
				}
			}

			$good_colors = array_unique($good_colors);
			$bad_colors = array_unique($bad_colors);

			// Règles CASE 4
			// Si on a trouvé 3 bads... on WIN
			if( count($bad_colors) >= 3 )
			{
				$test_try = $colors;
				#echo implode(', ', $test_try);
				foreach( $bad_colors as $bad_color )
				{
					if (($searchkey = array_search($bad_color, $test_try)) !== false) {
					    //unset($still_colors[$key]);
					    array_splice($test_try, $searchkey, 1);
					}
				}
				#echo implode(', ', $test_try);
				if( 5 == simulate_verif_bons($test_try) )
				{	
					$win_case = 'CASE 4';
					$winner_try = $test_try;
					#die('CASE 3');
				}
			} 


			// TMP disabled - should keep bad color to keep steps to the lowest
			#$must_set_last_try_color_back = $last_try[ $last_try_col-1 ];
		}
		else if( $last_try_col > 1 ){
			// 0
			// Les deux était soit bons ou pas bons

			// Test de regle
			#// Si le nb de bons est 4, on suppose que les inconnus sont automatiquement tous les deux bons
			#if( $last_bons == 4 )
			#{
			#	$good_colors[] = $last_try[ $last_try_col-1 ];
			#	$good_colors[] = $try[ $last_try_col-1 ];
			#}

			// Appliquer une règle spécial pour déterminer si ils étaient bons ou pas bon
			// Dans cet ordre pour réutiliser le manquant en premier ensuite
			$mystere_added=0;
			if( isset($try[ $last_try_col-1 ]) && !in_array($try[ $last_try_col-1 ], $mystere_colors) )
			{
				$mystere_colors[] = $try[ $last_try_col-1 ];
				$mystere_added++;

				$bad_default_colors[] = $try[ $last_try_col-1 ];
			}

			if( isset($last_try[ $last_try_col-1 ]) && !in_array($last_try[ $last_try_col-1 ], $mystere_colors) )
			{
				$mystere_colors[] = $last_try[ $last_try_col-1 ];
				$mystere_added++;

				$bad_default_colors[] = $last_try[ $last_try_col-1 ];
			}

			if( $mystere_added == 0 )
			{
				#echo "remove";
			}

			if( $bons == 4  && count($mystere_colors) >= 2 ) // On sait que si le nb de bons est à au moins 4, et qu'on tombe sur +0 (aucun changement), les deux valeurs sont bads car l'inverse donnerait 6 bons (4+2)
			{
				
				$test_try = $try; // Tmp test copy
				#echo implode(', ', $test_try)."<br>";
				// Remplace dans $try les couleurs des deux inconnus
				foreach( $mystere_colors as $mystere_color )
				{
					if (($searchkey = array_search($mystere_color, $test_try)) !== false) {
					    //unset($still_colors[$key]);
					    array_splice($test_try, $searchkey, 1);
					}
				}
				#echo implode(', ', $test_try)."<br>";
				$still_colors_count = count($still_colors);
				for( $t=$still_colors_count-1; $t>=0; $t-- )
				{
					$test_try[] = $still_colors[$t];
					#echo implode(', ', $test_try)."<br>";
					if( 5 == simulate_verif_bons($test_try) )
					{	
						$win_case = 'CASE 3';
						$winner_try = $test_try;
						#die('CASE 3');
					}
				} 
			}

			$good_colors = array_unique($good_colors);
			$bad_colors = array_unique($bad_colors);
			$bad_default_colors = array_unique($bad_default_colors);
		}
	}

	result_color_line($try, $bons);

	$last_try = $try;

	return $bons;
}

function next_try(&$last_try, &$try_col)
{
	global $still_colors;
	global $depart;
	global $mystere_colors;
	global $must_set_last_try_color_back;

	$next_try = $last_try; // Copy

	$still_colors_count = count($still_colors);

	#echo implode(', ', $still_colors)."(".count($still_colors).")"."<br>";
	#echo "<code>".$still_colors[$still_colors_count-1]."</code>";exit;

	#echo implode(', ', $next_try)."<br>";
	#echo "<br>".$try_col;
	if( count($mystere_colors) >= 2  )
	{
		#result_line("TRY MYSTERE ".end($mystere_colors));
		$next_try[ $try_col-1 ] = array_pop($mystere_colors);
		array_pop($mystere_colors); // IMPORTANT Again to clear las 2 added
	}
	
	else if( $still_colors_count == 0 )
	{
		if($debug) echo '<br>No more still color';
		$next_try[ $try_col-1 ] = array_pop($mystere_colors);	
	}
	else
	{
		#result_line("ELSE ".$try_col);
		$next_try[ $try_col-1 ] = $still_colors[$still_colors_count-1];

		#echo implode(', ', $next_try)."<br>";exit;

		#unset($still_colors[ $still_colors_count-1 ]);
		array_splice($still_colors, $still_colors_count-1, 1);
	}
	
	if( !is_null($must_set_last_try_color_back) )
	{
		$next_try[ $try_col ] = $must_set_last_try_color_back;
		$must_set_last_try_color_back=null;
	}
	
	return $next_try;
}

################################## MASTERMIND RESOLVER V1 ################################################

if( isset($_POST['action']) )
{
	$action = strip_tags($_POST['action']);
}

$debug=true;
$results = [];
#result_line('Depart');
$colors = ['black', 'grey', 'blue', 'yellow', 'orange', 'red', 'pink', 'green'];

$depart = array_random($colors, 5);
if( isset($_POST['depart']) )
{
	$post_depart = $_POST['depart'];
	$post_depart = array_unique($post_depart);
	if( count($post_depart) == 5 )
	{
		$depart = $post_depart;
	}
}

$cryptage = array_random($colors, 5);
if( isset($_POST['cryptage']) )
{
	$post_cryptage = $_POST['cryptage'];
	$post_cryptage = array_unique($post_cryptage);
	if( count($post_cryptage) == 5 )
	{
		$cryptage = $post_cryptage;
	}
}

$good_colors=[];
$bad_colors=[];
$mystere_colors=[];
$bad_default_colors=[]; // Quand retourne 0 (soit tous les deux bon ou mauvais, si validation qu'un des deux est mauvais, l'autre est considéré mauvais alors ajoutné au bads)

$still_colors = $colors; // Toutes les couleurs moins celles dans depart
foreach( $depart as $key => $color )
{
	if (($key = array_search($color, $still_colors)) !== false) {
	    //unset($still_colors[$key]);
	    array_splice($still_colors, $key, 1);
	}
}

$last_try=null;
$last_try_col=null;
$last_bons=null; // Pour connaitre le dernier changement et savoir comment appliquer la règle
$must_set_last_try_color_back=null;

$win=false;
$winner_try=null;

$bons = verif_bons($depart);
$last_try = $depart;
$last_bons = $bons;

if( $bons == 5) {
	$win = true;
}

$win_case=null;

if( !$win )
{
	$try_count=1;
	$max_loop=12;
	while( !$win || $try_count < 20 || $last_try_col > 1 || is_null($last_try_col) )
	{
		$try_count++;
		if( $try_count >= $max_loop )
		{
			result_line('FAILED');
			break;
		}

		if( $last_try_col > 1 )
		{
			$last_try_col--;
		} 
		else if( is_null($last_try_col) ) {
			$last_try_col = 5; // First try
		}
		else if( $last_try == 0 )
		{
			// Last try
			die('No try left');
		}

		if( is_null($winner_try) )
		{
			$win_case = '';
			$try = next_try($last_try, $last_try_col);
		}
		else
		{
			$try = $winner_try;
		}
		
		$bons = verif_bons($try);

		if( $bons == 5) 
		{
			#if($debug) echo implode(', ', $try);
			$win = true;

			break;
		}
		else
		{
			// Try some tests based on the good number count, the know bads et the know goods
			if( $bons >= 3 )
			{
				// If a bad is present in try, replace with a good and test for win
				if( ($bad_colors_count = count($bad_colors)) > 0 )
				{
					if( ($good_colors_count = count($good_colors)) > 0 )
					{
						$test_try = $try;
						for( $t=0; $t<$bad_colors_count; $t++ )
						{
							// Search 'bad' key and replace by good value
							if (($key = array_search($bad_colors[$t], $try)) !== false) {
							    
							    // For each goods
							    for( $t2=0; $t2<$good_colors_count; $t2++ )
							    {
								    if( !in_array($good_colors[$t2], $test_try) )
							    	{
								    	
								    	#echo "################ $try_count # <br>";
								    	#echo implode(', ', $test_try)."<br>";
							    		$test_try[$key] = $good_colors[$t2];
								    	#echo implode(', ', $test_try)."<br>";

								    	if( 5 == simulate_verif_bons($test_try) )
										{
											$win_case = 'CASE 1';
											$winner_try = $test_try;
										}
										else
										{
											#echo "################<br>";
											#echo "nooo<br>";
											#echo "################<br>";
										}
										
									}
							    }
							    
							}
						}
					}

					// Let's dig deepper and test with good AND some mystere colors
					if( count($mystere_colors) > 0 )
					{
						$test_try = $try;
						#var_dump($test_try);
						for( $t=0; $t<$bad_colors_count; $t++ )
						{
							// Search 'bad' key and replace by good value
							if (($key = array_search($bad_colors[$t], $test_try)) !== false) {
							    array_splice($test_try, $key, 1);
							 }

						}
						#var_dump($test_try);exit;
						
						$missing_colors = count($bad_colors);

						#echo "TEST ".implode(', ', $test_try)."<br>";
						$bad_default_colors_copy = $bad_default_colors;
					    
					    for( $t2=$missing_colors; $t2>0; $t2-- )
					    {
					    	for( $t3=0; $t3<$good_colors_count; $t3++ )
						    {
							    if( !in_array($good_colors[$t3], $test_try) )
						    	{
					    			$test_try[] = $good_colors[$t3];
					    		}
					    	}

					    	if( count($test_try) < 5 )
					    	{
					    		$missing_colors = 5 - count($test_try);
					    		for( $t3=$missing_colors; $t3>0; $t3-- )
							    {
							    	$current_bad_default = array_pop($bad_default_colors_copy);
							    	if( !in_array($current_bad_default, $test_try) )
							    	{
						    			$test_try[] = $current_bad_default;
						    		}
							    }
					    	}
					    }

					    #echo "TEST ".implode(', ', $test_try)."<br>";
					    if( count($test_try) == 5 )
					    {
					    	#echo implode(', ', $test_try);
					    	if( 5 == simulate_verif_bons($test_try) )
							{	
								$win_case = 'CASE 2';
								$winner_try = $test_try;
							}
					    }
					    else
					    {
					    	#die('test_try count != 5 ('.count($test_try).')');
					    }
					}

				}
			}

			
		}

		$last_bons = $bons;
	
	}
}

if( $win )
{
	result_line('WIN');
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Mastermind</title>
<style type="text/css">
	body  {
		font-size: 14px !important;
	}
	tbody tr td {
		height: 30px;
	}

	thead tr:first-child td,
	tbody tr.winner td {
		height: 50px;
	}

	select {
		padding: 8px;
	}
	select option:not(:first-child) {
		padding: 4px;
		font-size: 30px;
	}

	.submitbutton {
		padding: 10px 30px;
		font-size: 22px;
		margin-top: 60px;
	}

	.color_black {
		background-color: #222 ;
		color: #fff;
	}
	.color_white {
		background-color: #fff ;
		color: #222;
	}
	.color_blue {
		background-color: blue ;
		color: #fff;
	}
	.color_red {
		background-color: #cc0000 ;
		color: #fff;
	}
	.color_green {
		background-color: green ;
		color: #fff;
	}
	.color_yellow {
		background-color: yellow ;
		color: #222;
	}
	.color_orange {
		background-color: orange;
		color: #222;
	}
	.color_pink {
		background-color: pink;
		color: #222;
	}

</style>
</head>
<body>

<?php
	if( isset($action) )
	{
?>
<table border="1">
	<thead>
		<tr>
			<td align="center">CODE</td>
			<td width="100px" style="background-color: <?php echo $cryptage[0] ?>">&nbsp;</td>
			<td width="100px" style="background-color: <?php echo $cryptage[1] ?>">&nbsp;</td>
			<td width="100px" style="background-color: <?php echo $cryptage[2] ?>">&nbsp;</td>
			<td width="100px" style="background-color: <?php echo $cryptage[3] ?>">&nbsp;</td>
			<td width="100px" style="background-color: <?php echo $cryptage[4] ?>">&nbsp;</td>
			<td></td>
		</tr>
		<tr>
			<td width="70" align="center">Essais</td>
			<td align="center">
				1
			</td>
			<td align="center">
				2
			</td>
			<td align="center">
				3
			</td>
			<td align="center">
				4
			</td>
			<td align="center">
				5
			</td>
			<td align="center" width="100">Nombre de bons</td>
			<td align="center" width="200" style="background-color: #cc0000; color: #fff;">Mauvaises</td>
			<td align="center" width="300" style="background-color: green; color: #fff;">Bonnes</td>
			<td align="center" width="280" style="background-color: yellow; color: #222;">Inconnues</td>
			<td align="center" width="280" style="background-color: yellow; color: #222;">Bad combinaisons</td>
		</tr>
	</thead>
	<tbody>
		<?php
		for( $l=count($results); $l>=0; $l-- )
		{
			echo $results[$l];
		}
		?>
	</tbody>
	
</table>

<?php

	// Show first try if debug nedded
	foreach ($depart as $key => $value) {
		$depart_values .= '\''.$value.'\',';
	}
	$depart_values = substr($depart_values, 0, -1);
	echo '$depart = ['.$depart_values.'];'."<br>";

	// Show actual code too
	foreach ($cryptage as $key => $value) {
		$cryptage_values .= '\''.$value.'\',';
	}
	$cryptage_values = substr($cryptage_values, 0, -1);
	echo '$cryptage = ['.$cryptage_values.'];';
}
?>

<form method="post">
	<input type="hidden" name="action" value="play">

	<h1 style="text-align: center;">.:|~~~ &nbsp; MASTERMIND &nbsp; ~~~| :.</h1>

	<table align="center">
		<tr>
			<td colspan="5" align="center">
				<h2>Choisir le code du cryptologue...</h2>
			</td>
		</tr>
		<tr>
			<td>
				<select name="cryptage[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['cryptage']) && $_POST['cryptage'][0] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="cryptage[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['cryptage']) && $_POST['cryptage'][1] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="cryptage[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['cryptage']) && $_POST['cryptage'][2] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="cryptage[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['cryptage']) && $_POST['cryptage'][3] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="cryptage[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['cryptage']) && $_POST['cryptage'][4] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
		</tr>

		<tr>
			<td colspan="5" align="center" style="padding-top: 40px;">
				<h2>Premier code décrypteur...</h2>
			</td>
		</tr>
		<tr>
			<td>
				<select name="depart[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['depart']) && $_POST['depart'][0] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="depart[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['depart']) && $_POST['depart'][1] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="depart[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['depart']) && $_POST['depart'][2] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="depart[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['depart']) && $_POST['depart'][3] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<select name="depart[]">
					<?php
					echo '<option value="">Choisir...</option>';
					foreach ($colors as $key => $color) {
						echo '<option class="color_'.$color.'" '.(isset($_POST['depart']) && $_POST['depart'][4] == $color ? 'selected' : '').' vlaue="'.$color.'">'.$color.'</option>';
					}
					?>
				</select>
			</td>
		</tr>

	</table>

	<center>
		<button class="submitbutton" type="submit">Nouvelle partie</button>
	</center>

</form>
</body>
</html>
