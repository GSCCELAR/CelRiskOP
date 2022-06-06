<?php
	class FormularioReportePDF extends FPDF {

		private $form;
		private $colores = array(243,252);
		private $colores_index = 0;
		private $poner_colores = false;
		public static $ancho_imagen = 34;
		private $widths = array();
		private $aligns = array();

		function __construct($f) {
			$this->form = $f;
			$this->FPDF('P', 'mm', 'letter');
			$this->AliasNbPages();
			$this->SetDisplayMode("fullwidth");
			$this->SetAutoPageBreak(true, 15);
			$this->SetTitle("Reporte: " . $this->form->getCampo("tiporeporte_nombre"));
			$this->SetSubject("Reporte Celrisk");
			$this->SetAuthor("GSC");
			$this->SetFont("Arial", "", "11");
			$this->SetMargins(15, 15);
		}

		public function Header() {
			$widths = $this->widths;
			$aligns = $this->aligns;
			$fontsize = $this->FontSizePt;
			$fontstyle = $this->FontStyle;
			$colores = $this->poner_colores;

			$this->widths = array(30,160);
			$this->aligns = array('L', 'C');
			$this->poner_colores = false;
			$this->SetFont('Arial', 'B', 15);
			$this->Row(array("", "\n\n\n"), 1, 9);

			$this->Image(DIR_APP . 'imagenes/logo_top.png', 21, 16, 19 );

			$this->SetXY(55, 17);
			$this->MultiCell(140, 25, "REPORTE ".mb_strtoupper($this->form->getCampo("tiporeporte_nombre"), 'ISO-8859-1'), 0, 'C');

			/*$this->SetXY(165, 15);
			$this->widths = array(18,22);
			$this->aligns = array('L', 'C');
			$this->SetFont('Arial', '', 10);
			$this->Row(array("Código", "BO F 02-1"), 1, 6);
			$this->SetX(165);
			$this->Row(array("Versión", "1"), 1, 6);
			$this->SetX(165);
			$this->Row(array("Vigencia", "19/07/2018"), 1, 6);*/

			$this->widths = $widths;
			$this->aligns = $aligns;
			$this->poner_colores = $colores;
			$this->SetFont('Arial', $fontstyle, $fontsize);
			$this->SetY(56);
		}

		public function Footer() {
			$this->SetY(-15);
			$fontsize = $this->FontSizePt;
			$fontstyle = $this->FontStyle;

			$this->SetFont('Arial', '', 10);
			$this->Cell(0, 5, 'Página ' . $this->PageNo() . ' de {nb}', 0, 0, 'C');

			$this->SetFont('Arial', $fontstyle, $fontsize);
		}

		public function registroFotografico() {
			
			$this->SetFont('Arial','B',15);
			$this->Ln(6);
			$this->Line(15, $this->GetY()+9, 205, $this->GetY()+9);
			$this->Write(10,"REGISTRO FOTOGRÁFICO");
			$this->Ln(10);
			
			$this->colores_index = 0;
			$this->widths = array(190);
			$this->aligns = array("L");
			$this->poner_colores = false;
			$this->SetFont('Arial','B',11);
			$fotos = array();
			$lista = array();
			$ruta = RUTA_SOPORTES. $this->form->id . "/imagenes/";
			$d = @dir($ruta);
			if ($d) {
				$tr = array();
				while($df = $d->read()) {
					if ($df == "." || $df == "..") 
						continue;
					if (is_file($d->path . $df)) {
						$nombre = utf8_decode(base64_decode(basename($df, ".jpg")));
						$fotos[] = $d->path . $df;
						if (count($fotos) == 4) {
							$lista[] = $fotos;
							$fotos = array();
						}
					}
				}
			}
			if (count($fotos) > 0) {
				$lista[] = $fotos;
				$fotos = array();
			}
			$this->widths = array(47, 47, 47, 47);
			foreach($lista as $linea) {
				$this->SetFont('Arial', '', 11);
				$this->RowFotos($linea, 0);
			}
		}

		public function datosGenerales() {
			$this->AddPage();
			$this->widths = array(50,  140);
			$this->aligns = array("L", "L");
			$this->poner_colores = true;
			
			$this->SetFont('Arial','B',15);
			$this->Write(10,"DATOS GENERALES");
			$this->Line(15, $this->GetY() + 9, 205, $this->GetY() + 9);
			$this->SetFont('Arial','',11);
			$this->Ln(10);
			$this->Row(array("#ID del Reporte:", $this->form->id), 0);
			$this->Row(array("Fecha de recepción:", $this->form->getFecha("fecha_sistema")), 0);
			$this->Ln();
			$this->Row(array("Fecha Inicio del Reporte:", $this->form->getFecha('fecha_reporte')), 0);
			$this->Row(array("Cliente:", "Nit: ". $this->form->getCampo('cliente_identificacion')." - ".$this->form->getCampo('cliente_razonsocial')), 0);
			$this->Row(array("Sede:", $this->form->getCampo('sede_nombre')), 0);
			$this->Row(array("Dirección:", $this->form->getCampo('sede_direccion')), 0);
			$this->Row(array("Proceso:", $this->form->getCampo('proceso_nombre')), 0);
			$this->Row(array("Riesgo:", $this->form->getCampo('riesgo_nombre')), 0);
			$this->Row(array("Control:", $this->form->getCampo('control_nombre')), 0);
			$this->Row(array("Descripción:", $this->form->getCampo('descripcion')), 0);
			$this->poner_colores = false;
		}

		public function generarPDF($ruta = '', $dest = 'D') {
			$this->datosGenerales();
			$this->registroFotografico();
			if ($ruta == '')
				$ruta = $this->form->getCampo('tiporeporte_nombre') . " #" . $this->form->id . " ". $this->form->getCampo("cliente_razonsocial")  . ".pdf";
			$this->Output($ruta, $dest);
		}

		/** 
		 * Adiciona una fila a la tabla creada en el PDF 
		 */
		function Row($data, $borde = 1, $altura = 6.5) {
		    $nb=0;
		    for($i=0;$i<count($data);$i++)
		        $nb=max($nb, $this->NbLines($this->widths[$i], $data[$i]));
		    $h=$altura*$nb;
			$this->CheckPageBreak($h);
			if ($this->poner_colores)
				$this->SetFillColor($this->colores[$this->colores_index++ % 2]);
		    for($i=0;$i<count($data);$i++) {
		        $w=$this->widths[$i];
		        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		        $x=$this->GetX();
		        $y=$this->GetY();
				if ($borde)
					$this->Rect($x, $y, $w, $h);
				if ($this->poner_colores)
					$this->MultiCell($w, $altura, $data[$i], 0, $a, true);
				else
					$this->MultiCell($w, $altura, $data[$i], 0, $a);
		        $this->SetXY($x+$w, $y);
		    }
		    $this->Ln($h);
		}

		public function RowFotos($data, $borde = 1, $altura = 6.5) {
		    $nb=0;
		    $ancho = $alto = $nb = 0;
		    if (is_file($data[0])) {
				list($ancho, $alto) = $this->getAnchoAltoImagen($data[0]);
				$alto =  $this->widths[0] / $ancho * $alto;
			}
		    for($i = 1; $i < count($data); $i++)
		        $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
			$h = max(4 * $nb, $alto);
			$this->CheckPageBreak($h);
			$nombres_fotos = array();
		    for($i=0;$i<count($data);$i++) {
		        $w=$this->widths[$i];
		        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		        $x=$this->GetX();
		        $y=$this->GetY();
				if ($borde)
					$this->Rect($x, $y, $w, $h);
				if (is_file($data[$i])) {
					$this->Image($data[$i], $x+1, $y+1, $this->widths[$i] - 1);
					$nombres_fotos[] = utf8_decode(base64_decode(basename($data[$i], ".jpg")));
				}
		        $this->SetXY($x+$w, $y);
			}
			$this->Ln($h);
		}

		
		/**
		 * Carga una imagen en una instancia de la clase SimpleImage() y retorna en un array
		 * el ancho y alto de esta.
		 * 
		 * @param object $imagen [optional]
		 * @return		array(ancho, alto) <-- datos de la imagen 
		 */
		function getAnchoAltoImagen($imagen = "") {
			if (!is_file($imagen))
				return 1;
			$img = new SimpleImage();
			$img->load($imagen);
			return array($img->getWidth(), $img->getHeight());
		}
		
		/**
		 * Adiciona una fila con imagen en el PDF, teniendo en cuenta que el primer item de array $data
		 * contiene el directorio de la imagen, en caso de no existir la imagen dejamos el espacio en blanco
		 * 
		 * La altura para crear el cuadro es calculada proporcional al tamaño de self::$ancho que tendrá la
		 * imagen, la proporción de la altura es [self::$ancho / ancho_imagen * alto_imagen]
		 * @param object $data
		 * @return 
		 */
		function RowImagen($data) {
		    $ancho = $alto = $nb = 0;
		    if (is_file($data[0])) {
				list($ancho, $alto) = $this->getAnchoAltoImagen($data[0]);
				$alto = self::$ancho_imagen / $ancho * $alto;
			}
		    for($i = 1; $i < count($data); $i++)
		        $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
		    $h = max(4 * $nb, $alto + 2);
		    if ($this->CheckPageBreak($h)) {
		    	//si adicionó una nueva página, ponemos de nuevo la cabecera de los items de la cotización
				//Cabecera
				$titulos_cotizacion = array("ITEM", "DESCRIPCIÓN (Equipos)", "CANT.", "\$ UNID", "\$ TOTAL");
				
				//Preparamos la Tabla para los títulos
				$this->SetFillColor(118,202,35);
			    $this->SetTextColor(255);
			    $this->SetFont('','B', 10);
				
				$this->Ln();
			    for($i=0; $i<count($titulos_cotizacion);$i++)
			        $this->Cell($this->widths[$i], 6, $titulos_cotizacion[$i], 1, 0, 'C', 1);
			    $this->Ln();
				 //Restauración de colores y fuentes
				$this->SetFillColor(249);
				$this->SetTextColor(0);
				$this->SetFont('','', 9);
				
		    }
		    for($i = 0; $i < count($data); $i++) {
		        $w=$this->widths[$i];
		        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		        $x=$this->GetX();
		        $y=$this->GetY();
				if ($i == 0) {
					if (is_file($data[$i]))
						$this->Image($data[$i], $x+1, $y+1, self::$ancho_imagen, self::$alto_imagen);
				}
		        else
					$this->MultiCell($w, 4, $data[$i], 0, $a);
				$this->Rect($x, $y, $w, $h);
		        $this->SetXY($x+$w, $y);
		    }
		    $this->Ln($h);
		}
		
		/**
		 * Calcula de acuerdo a un tamaño $h que ocupará de alto el próximo texto a escribir en el PDF
		 * si es necesario pasar a la próxima página o no de acuerdo al valor depagebreakertrigger del PDF, en caso de 
		 * que sea necesario pasar a la próxima página la crea y retorna TRUE, en caso contrario retorna
		 * FALSE indicando que no es necesario crear una nueva página para escribir el texto con altura $h
		 *  
		 * @param object $h
		 * @return 
		 */
		function CheckPageBreak($h) {
		    if($this->GetY()+$h>$this->PageBreakTrigger) {
		        $this->AddPage($this->CurOrientation);
				return true;
			}
			return false;
		}
		
		function NbLines($w, $txt) {
		    $cw = &$this->CurrentFont['cw'];
		    if ($w==0)
		        $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s=str_replace("\r", '', $txt);
		    $nb=strlen($s);
		    if($nb>0 and $s[$nb-1]=="\n")
		        $nb--;
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb) {
		        $c=$s[$i];
		        if($c=="\n") {
		            $i++;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		            continue;
		        }
		        if($c==' ')
		            $sep=$i;
		        $l+=$cw[$c];
		        if($l>$wmax) {
		            if($sep==-1) {
		                if($i==$j)
		                    $i++;
		            }
		            else
		                $i=$sep+1;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		        }
		        else
		            $i++;
		    }
		    return $nl;
		}
		
	}
?>