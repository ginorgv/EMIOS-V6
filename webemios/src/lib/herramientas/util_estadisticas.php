<?php
	function dame_valor_quintil($quintil, $valores_lorenz)
	{
		$numero_valores = count($valores_lorenz) - 1;
		if ($quintil < $valores_lorenz[0][0])
		{
			return ($valores_lorenz[0][1]);
		}
		elseif ($quintil > $valores_lorenz[$numero_valores][0])
		{
			return ($valores_lorenz[$numero_valores][1]);
		}

		$indice = 0;
		while ($valores_lorenz[$indice][0] < $quintil)
		{
			$indice++;
		}

		if ($valores_lorenz[$indice][0] == $quintil)
		{
			return ($valores_lorenz[$indice][1]);
		}
		else
		{
			$x0 = $valores_lorenz[$indice - 1][0];
			$x1 = $valores_lorenz[$indice][0];
			$y0 = $valores_lorenz[$indice - 1][1];
			$y1 = $valores_lorenz[$indice][1];
			$x = $quintil - $x0;
			$y = $y0 + (($y1 - $y0) * ($x) / ($x1 - $x0));

			return ($y);
		}
	}
?>
