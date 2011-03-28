<?php

	ini_set("memory_limit","128M");
    ini_set("safe_mode", "0");
    ini_set("safe_mode_gid", "0");

	$rij = explode("/", $_SERVER["SCRIPT_NAME"] . $_SERVER["PATH_INFO"]); //PHP_SELF kan soms ook worden gebruikt (?)
	$rij = array_reverse($rij);
	$naamgevonden = false;
	$gevondennaam  = "";
	$gevondenx = "";
	$gevondeny = "";
    $restURL = "";
	foreach($rij as $naam) {
		if ($naamgevonden == false AND preg_match("@[a-zA-Z0-9_\-\.]\.(?:png|jpg|jpeg|bmp|gif)@i",$naam) ) {
			$naamgevonden = true;
			$gevondennaam = $naam;
		}
		if ($naamgevonden == true AND is_numeric($naam)) {
			//als er al een x gevonden is, en er blijkt nóg een getal te zijn, dat is het gevonden getal dus de x en de oude x de y
			//het formaat is namelijk als x/y/naam.ext
			if (!empty($gevondenx)) {
				$gevondeny = $gevondenx;
				$gevondenx = $naam;
			} else {
				//een getal gevonden direct na de naam
				$gevondenx = $naam;
			}
		} else if ($naamgevonden == true AND !empty($gevondenx)) {
            if ($naam == "resizeimages.php") {
                break; //dan is alle te vinden informatie dus gevonden
            }
            //restURL vóór de gevonden getallen
            $restURL = $naam . "/" . $restURL;
        }
	}
	
	if ( !empty($gevondennaam) AND !empty($gevondenx) ) {
		//kijken of opgevraagde afbeelding bestaat in de hoofdmap
		if (file_exists($restURL . $gevondennaam)) {
			//als alleen de x opgegegen is, moeten we natuurlijk gevondeny niet meenemen in de locatie
				if (empty($gevondeny)) {
					//er is geen max y gevonden
					resize_and_upload_image($restURL . $gevondennaam, Array("maxx"=>$gevondenx, "maxy"=>$gevondeny),$restURL . $gevondenx . "/" . $gevondennaam);
					header('Content-type: image');
					readfile($restURL . $gevondenx . "/" . $gevondennaam);
				} else {
					//er is een max y gevonden
					resize_and_upload_image( $restURL . $gevondennaam, Array("maxx"=>$gevondenx, "maxy"=>$gevondeny),$restURL . $gevondenx . "/" . $gevondeny . "/" . $gevondennaam);
					header('Content-type: image');
					readfile($restURL . $gevondenx . "/" . $gevondeny . "/" . $gevondennaam);
				}
		} else {
			echo "afbeelding bestaat niet";
		}
	} else {
        echo "afbeelding bestaat niet";
    }
	
	function resize_and_upload_image( $locatieoud, $size = Array("maxx", "maxy", "perc"), $locatie)
	{
		//dmv extensie bepalen...
		//$type = strtolower(substr($locatieoud, strrpos($locatieoud,".")+1 ));
		//if ($type == "jpg") { $type = "jpeg"; }
		
		//dmv MIME bepalen
		$info = getimagesize( $locatieoud );
		$type =  str_replace( 'image/', '', $info["mime"] );
		$createFunc = 'imagecreatefrom' . $type;
	   
		$im = $createFunc( $locatieoud );
	   
		$w = $info[ 0 ];
		$h = $info[ 1 ];
		// create thumbnail
		if (!empty($size["perc"])) {
			$percentage = $size["perc"];
			$tw = round($percentage / 100 * $w);
			$th = round($percentage / 100 * $h);
		} else {
			$tw = $size["maxx"];
			if (empty($size["maxy"])) {
				$th = 9999999999; //er is geen limiet op de y
			} else {
				$th = $size["maxy"];
			}
		}
		//$imT = imagecreatetruecolor( $tw, $th );
		//als de doelresoluties op x en y groter zijn dan de resoluties van het bronbestand, dan gewoon het bronbestand tonen
		if ($tw >= $w AND $th >= $h) {
			
		}
	   
		if ( $tw/$th < $th/$tw )
		{ // wider
			$tmph = $h*($tw/$w);
			$imT = imagecreatetruecolor( $tw, $tmph );
            //transparancy!
            if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
              $trnprt_indx = imagecolortransparent($im);
         
              // If we have a specific transparent color
              if ($trnprt_indx >= 0) {
         
                // Get the original image's transparent color's RGB values
                $trnprt_color    = imagecolorsforindex($im, $trnprt_indx);
         
                // Allocate the same color in the new image resource
                $trnprt_indx    = imagecolorallocate($imT, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
         
                // Completely fill the background of the new image with allocated color.
                imagefill($imT, 0, 0, $trnprt_indx);
         
                // Set the background color for new image to transparent
                imagecolortransparent($imT, $trnprt_indx);
         
         
              } 
              // Always make a transparent background color for PNGs that don't have one allocated already
              elseif ($info[2] == IMAGETYPE_PNG) {
         
                // Turn off transparency blending (temporarily)
                imagealphablending($imT, false);
         
                // Create a new transparent color for image
                $color = imagecolorallocatealpha($imT, 0, 0, 0, 127);
         
                // Completely fill the background of the new image with allocated color.
                imagefill($imT, 0, 0, $color);
         
                // Restore transparency blending
                imagesavealpha($imT, true);
              }
            }

            
            
			imagecopyresampled( $imT, $im, 0, 0, 0, 0, $tw, $tmph, $w, $h ); // resize to width
			//imagecopyresampled( $imT, $temp, 0, 0, 0, $tmph/2-$th/2, $tw, $th, $tw, $th ); // crop
		}else
		{ // taller
			$tmpw = $w*($th/$h );
			$imT = imagecreatetruecolor( $tmpw, $th );
			imagecopyresampled( $imT, $im, 0, 0, 0, 0, $tmpw, $th, $w, $h ); // resize to height
		   // imagecopyresampled( $imT, $temp, 0, 0, $tmpw/2-$tw/2, 0, $tw, $th, $tw, $th ); // crop
		}
	   
		// save the image
		$saveFunc = 'image' . $type;
        if ($type == "png") { $quality = 9; } else { $quality = 100; }
        rmkdir(dirname($locatie), 0777); //create dir
		$saveFunc( $imT, $locatie, $quality );
		/*) {
			return true;
		} else {
			return false;
		}*/
		
		return true;
	}
	
	/**
	 * Makes directory and returns BOOL(TRUE) if exists OR made.
	 *
	 * @param  $path Path name
	 * @return bool
	 */
	function rmkdir($path, $mode = 0777) {
		$path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
		$e = explode("/", ltrim($path, "/"));
		if(substr($path, 0, 1) == "/") {
			$e[0] = "/".$e[0];
		}
		$c = count($e);
		$cp = $e[0];
		for($i = 1; $i < $c; $i++) {
			if(!is_dir($cp) && !@mkdir($cp, $mode)) {
				return false;
			}
			$cp .= "/".$e[$i];
		}
		return @mkdir($path, $mode);
	}
	
	//make createimagefrombmp() work
	function ConvertBMP2GD($src, $dest = false) {
        if(!($src_f = fopen($src, "rb"))) {
        return false;
        }
        if(!($dest_f = fopen($dest, "wb"))) {
        return false;
        }
        $header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,
        14));
        $info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
        fread($src_f, 40));

        extract($info);
        extract($header);

        if($type != 0x4D42) { // signature "BM"
        return false;
        }

        $palette_size = $offset - 54;
        $ncolor = $palette_size / 4;
        $gd_header = "";
        // true-color vs. palette
        $gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
        $gd_header .= pack("n2", $width, $height);
        $gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
        if($palette_size) {
        $gd_header .= pack("n", $ncolor);
        }
        // no transparency
        $gd_header .= "\xFF\xFF\xFF\xFF";

        fwrite($dest_f, $gd_header);

        if($palette_size) {
        $palette = fread($src_f, $palette_size);
        $gd_palette = "";
        $j = 0;
        while($j < $palette_size) {
        $b = $palette{$j++};
        $g = $palette{$j++};
        $r = $palette{$j++};
        $a = $palette{$j++};
        $gd_palette .= "$r$g$b$a";
        }
        $gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
        fwrite($dest_f, $gd_palette);
        }

        $scan_line_size = (($bits * $width) + 7) >> 3;
        $scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
        0x03) : 0;

        for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
        // BMP stores scan lines starting from bottom
        fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
        $l));
        $scan_line = fread($src_f, $scan_line_size);
        if($bits == 24) {
        $gd_scan_line = "";
        $j = 0;
        while($j < $scan_line_size) {
        $b = $scan_line{$j++};
        $g = $scan_line{$j++};
        $r = $scan_line{$j++};
        $gd_scan_line .= "\x00$r$g$b";
        }
        }
        else if($bits == 8) {
        $gd_scan_line = $scan_line;
        }
        else if($bits == 4) {
        $gd_scan_line = "";
        $j = 0;
        while($j < $scan_line_size) {
        $byte = ord($scan_line{$j++});
        $p1 = chr($byte >> 4);
        $p2 = chr($byte & 0x0F);
        $gd_scan_line .= "$p1$p2";
        } $gd_scan_line = substr($gd_scan_line, 0, $width);
        }
        else if($bits == 1) {
        $gd_scan_line = "";
        $j = 0;
        while($j < $scan_line_size) {
        $byte = ord($scan_line{$j++});
        $p1 = chr((int) (($byte & 0x80) != 0));
        $p2 = chr((int) (($byte & 0x40) != 0));
        $p3 = chr((int) (($byte & 0x20) != 0));
        $p4 = chr((int) (($byte & 0x10) != 0));
        $p5 = chr((int) (($byte & 0x08) != 0));
        $p6 = chr((int) (($byte & 0x04) != 0));
        $p7 = chr((int) (($byte & 0x02) != 0));
        $p8 = chr((int) (($byte & 0x01) != 0));
        $gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
        } $gd_scan_line = substr($gd_scan_line, 0, $width);
        }

        fwrite($dest_f, $gd_scan_line);
        }
        fclose($src_f);
        fclose($dest_f);
        return true;
    }

    function imagecreatefrombmp($filename) {
        $tmp_name = tempnam("/tmp", "GD");
        if(ConvertBMP2GD($filename, $tmp_name)) {
        $img = imagecreatefromgd($tmp_name);
        unlink($tmp_name);
        return $img;
        } return false;
    }

?>
