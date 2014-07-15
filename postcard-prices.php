<?php

// using OOP to get some pricing

// set some numbers that don't change:
$clickCharge = 0.059;

// Holds the papers & figures out cost per sheet
class paper {
	public $brand = "";
	public $size = "";
	//public $weight = "";
	public $cost_per = 0;
	public $cost_unit = 0;

	function __construct($brand, $size, $cost_per, $unit) {
		$this->brand = $brand;
		$this->size = $size;
		$this->cost_per = $cost_per;
		$this->cost_unit = $unit;
	}

	public function perSheet() {
		$per = $this->cost_per / $this->cost_unit;
		return $per;
	}
}

// Get the price for a set of post cards
class PostCardSet
{
	/*
	public $parent_size = " ";
	public $papercost = 0.0;
	public $qty = 0;
	*/
	private $parent_size;
	private $parent_yeild;
	private $final_size;
	private $bleed;
	private $paper;
	private $paperCost;
	private $ammount;
	public $cost;
	private $click;


	private function getCost ()
	{
		// localize the needed variables:
		$perSheet = $this->paperCost;
		$qty = $this->ammount;
		$yeild = $this->parent_yeild;
		$click = $this->click;

		// do the math:
		$calcP = ($perSheet + 2 * $click) * ( round( ( $qty / $yeild ) , 0, PHP_ROUND_HALF_UP) );

		return $calcP;
	}

	private function getYeildAndSize () {
		// returns an array of two values:
		// first is the parent sheet size
		// second is the yeild from one sheet
		//
		// calculated based on experementation

		// for simplicity:
		$final_size = $this->final_size;
		$bleed = $this->bleed;

		if ($final_size == "4x6" && $bleed == false) {
			$this->parent_size = "12x18";
			$this->parent_yeild = 9;
			return true;
		}
		elseif ($final_size == "4x6" && $bleed == true) {
			$this->parent_size = "11x17";
			$this->parent_yeild = 6;
			return true;
		}
		elseif ($final_size == "4.25x5.5" && $bleed == false) {
			$this->parent_size = "11x17";
			$this->parent_yeild = 8;
			return true;
		}
		elseif ($final_size == "4.25x5.5" && $bleed == true) {
			$this->parent_size = "12x18";
			$this->parent_yeild = 8;
			return true;
		}
		elseif ($final_size == "5x7") {
			$this->parent_size = "11x17";
			$this->parent_yeild = 4;
			return true;
			// both bleed and nonbleed are the same
		}
		elseif ($final_size == "5.5x8.5" && $bleed == false) {
			$this->parent_size = "11x17";
			$this->parent_yeild = 4;
			return true;
		}
		elseif ($final_size == "5.5x8.5" && $bleed == true) {
			$this->parent_size = "12x18";
			$this->parent_yeild = 4;
			return true;
		}
		else {
			return false;
		}
	}

	private function getPaperCost ($gloss) {
		// What is the parent sheet size?
		$size = $this->parent_size;
		$paper = $this->paper;

		if ( $size == "11x17" && $gloss == true ) {
			$ret = $paper[0]->perSheet();
			return $ret;
		}
		elseif ( $size == "12x18" && $gloss == true ) {
			$ret = $paper[1]->perSheet();
			return $ret;
		}
		elseif ( $size == "11x17" && $gloss == false ) {
			$ret = $paper[2]->perSheet();
			return $ret;
		}
		elseif ( $size == "12x18" && $gloss == false ) {
			$ret = $paper[3]->perSheet();
			return $ret;
		}
	}
	
	function __construct($cardsize, $q, $bleed, $gloss, $paper, $click)
	{
		// plug in the numbers:
		$this->final_size = $cardsize;
		$this->ammount = $q;
		$this->bleed = $bleed;
		$this->paper = $paper;
		$this->click = $click;

		$this->getYeildAndSize();
		// Next, get the paper to use
		$this->paperCost = $this->getPaperCost($gloss);

		// Now calculate the cost!
		$this->cost = $this->getCost();
	}

}

function retailPrice($cost) {
	// -1.35*ln( COST ) + 9.5
	// take that and multiply it by cost
	// then add by cost
	// and round to the penny
	$retailfor = round(( $cost + ($cost * ( 9.5 + (-1.35) * ( log($cost)) ) ) ),2);

	return $retailfor;
}


// set some paper prices manually
//   maybe I can eventually abstact this out to a DB
$paper = array();
$paper[0] = new paper ("Gloss", "11x17", 57.61, 1000);
$paper[1] = new paper ("Gloss", "12x18", 65.55, 1000);
$paper[2] = new paper ("Accent", "11x17", 79.28, 1000);
$paper[3] = new paper ("Accent", "12x18", 91.56, 1000);

// set up our postcard qty
$qty = array();
$qty[0] = 125;
$qty[1] = 250;
$qty[2] = 500;
$qty[3] = 1000;
$qty[4] = 2500;

// set up our postcard sizes
$cardSize = array(0 => "4.25x5.5", 1 => "4x6", 2 => "5x7", 3 => "5.5x8.5" );

$cardset = array();

/*
// just for testing: dump everything:
foreach ($cardSize as $cs) {
	foreach ($qty as $qn => $q) {
		// which paper to use?
		// glossy
		// first do with no bleed:
		$bleed = false;
		$glossy = true;
		$temp = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
		var_dump($temp);

		echo "bleed:" . $bleed . "<br/>";
		echo "gloss:" . $glossy . "<br/>";
		echo "<br>" . $temp->cost;

		echo "<br/><br/>";

		// now do WITH bleed
		$bleed = true;
		$glossy = true;
		$temp2 = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
		var_dump($temp2);

		echo "bleed:" . $bleed . "<br/>";
		echo "gloss:" . $glossy . "<br/>";
		echo "<br>" . $temp2->cost;

		echo "<br/><br/>";

				// not glossy
		// first do with no bleed:
		$bleed = false;
		$glossy = false;
		$temp = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
		var_dump($temp);

		echo "bleed:" . $bleed . "<br/>";
		echo "gloss:" . $glossy . "<br/>";
		echo "<br>" . $temp->cost;

		echo "<br/><br/>";

		// now do WITH bleed
		$bleed = true;
		$glossy = false;
		$temp2 = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
		var_dump($temp2);

		echo "bleed:" . $bleed . "<br/>";
		echo "gloss:" . $glossy . "<br/>";
		echo "<br>" . $temp2->cost;

		echo "<br/><br/>";
	}
}

/* 
			##GLOSSY
	Qtys	|	4.25x5.5	|	4x6	...
	----------------------------------
	 125	|	[Non Bleed]	|	[Non Bleed]
	 		|	[Bleed]		|	[Bleed]

	 		##NOT GLOSSY
	Qtys	|	4.25x5.5	|	4x6	...
	----------------------------------
	 125	|	[Non Bleed]	|	[Non Bleed]
	 		|	[Bleed]		|	[Bleed]
*/
?><!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		<title>PostCard Costs &#8226; The UPS Store #4955 </title>
		<meta name="description" content="just a little coding exercise to do some math">

		<link rel="stylesheet" href="ups-table-style.css">

	</head>
	<body>
		<header>
			<h1>Postcards!</h1>
		</header>
<table>
	<tr><th>Size</th></tr>
	<tr><td class="nobleed">No Bleed</td></tr>
	<tr><td class="bleed">Bleed to Edge</td></tr>
</table>


<?php
for ($price_cost_loop=0; $price_cost_loop < 3; $price_cost_loop++) { 
	//this loop makes pricing first then
	// repeats everything for customer pricing
	// repeats for even numbers...



for ($i=0; $i < 2; $i++) { 
	// Make the table twice. Once for glossy, once for Heavy paper.

	// This translates 0/1 into true/false and "Glossy Cards"/"100# Cards"
	switch ($i) {
		case 0:
			$glossy = true;
			$tabletitle = "Glossy Cards";
			break;
		case 1:
			$glossy = false;
			$tabletitle = "100# Cover Cards";
			break;
		default:
			echo "oops...";
			break;
	}


?>
<div class="box">
<h1><?php echo $tabletitle; ?></h1> 
<table>
	<tr>
		<th>Quantity</th>
		<?php 

			foreach ($cardSize as $v) {
				echo "<th>" . $v . "</th>";
			}

		?>
  </tr>

	<?php
		foreach ($qty as $qn => $q) {
			echo "<tr>";
			echo "<td rowspan='2'>" . $q . "</td>";

			foreach ($cardSize as $cs) {
				echo "<td class='nobleed'>";
				$bleed = false; // first without bleed
				$cards = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
				echo "$";
				if($price_cost_loop == 0) {
					echo round($cards->cost,3);
				} 
				elseif($price_cost_loop == 1) { 
					// echo retailPrice($cards->cost);
					
					echo $q * round(retailPrice($cards->cost) / $q,2);
				}
				elseif ($price_cost_loop == 2) {
					echo round(retailPrice($cards->cost) / $q,2);
				}
				echo "</td>";
			}
			echo "</tr>";
			foreach ($cardSize as $cs) {
				echo "<td class='bleed'>";
				$bleed = true; // next with it
				$cards = new PostCardSet($cs, $q, $bleed, $glossy, $paper, $clickCharge );
				echo "$" ;
				if($price_cost_loop == 0) {
					echo round($cards->cost,3);
				} 
				elseif($price_cost_loop == 1) { 
					// echo retailPrice($cards->cost);
					echo $q * round(retailPrice($cards->cost) / $q,2);
				}
				elseif ($price_cost_loop == 2) {
					echo round(retailPrice($cards->cost) / $q,2);
				}
				echo "</td>";
			}

			echo "</tr>";
		}
	?>
</table>
</div>
<?php
} //end the 2-table loop
} //end the 4-table loop

?>

</body>
</html>

